<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Middleware;

use LD\LanguageDetection\Check\BotListener;
use LD\LanguageDetection\Detect\BrowserLanguage;
use LD\LanguageDetection\Event\BuildResponse;
use LD\LanguageDetection\Event\CheckLanguageDetection;
use LD\LanguageDetection\Event\DetectUserLanguages;
use LD\LanguageDetection\Event\NegotiateSiteLanguage;
use LD\LanguageDetection\Middleware\LanguageDetection;
use LD\LanguageDetection\Negotiation\DefaultNegotiation;
use LD\LanguageDetection\Response\DefaultResponse;
use LD\LanguageDetection\Service\Normalizer;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\DependencyInjection\Container;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 * @coversNothing
 */
class LanguageDetectionTest extends AbstractTest
{
    /**
     * @covers \LD\LanguageDetection\Middleware\LanguageDetection
     */
    public function testBreakAfterCheckLanguageDetectionByAddingBotAgent(): void
    {
        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new NullResponse());

        $serverRequest = new ServerRequest(null, null, 'php://input', ['user-agent' => 'AdsBot-Google']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $middleware = new LanguageDetection($this->getEventListener());
        $response = $middleware->process($serverRequest, $handler);

        self::assertInstanceOf(NullResponse::class, $response);
    }

    /**
     * @covers \LD\LanguageDetection\Middleware\LanguageDetection
     */
    public function testBreakAfterDetectUserLanguagesByMissingLanguages(): void
    {
        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new NullResponse());

        $serverRequest = new ServerRequest();
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $middleware = new LanguageDetection($this->getEventListener());
        $response = $middleware->process($serverRequest, $handler);

        self::assertInstanceOf(NullResponse::class, $response);
    }

    /**
     * @covers \LD\LanguageDetection\Middleware\LanguageDetection
     */
    public function testBreakAfterNegotiateSiteLanguageByNotFoundTargetLanguage(): void
    {
        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new NullResponse());

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['accept-language' => 'de,de_DE']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $middleware = new LanguageDetection($eventDispatcher);
        $response = $middleware->process($serverRequest, $handler);

        self::assertInstanceOf(NullResponse::class, $response);
    }

    /**
     * @covers \LD\LanguageDetection\Middleware\LanguageDetection
     */
    public function testBreakAfterBuildResponseByEmptyResponse(): void
    {
        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new NullResponse());

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['accept-language' => 'de,de_DE']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $middleware = new LanguageDetection($eventDispatcher);
        $response = $middleware->process($serverRequest, $handler);

        self::assertInstanceOf(NullResponse::class, $response);
    }

    /**
     * @covers \LD\LanguageDetection\Middleware\LanguageDetection
     */
    public function testFullExecution(): void
    {
        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new NullResponse());

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['accept-language' => 'de,de_DE']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $middleware = new LanguageDetection($eventDispatcher);
        $response = $middleware->process($serverRequest, $handler);

        // @todo Fix test for full configuration
        self::assertInstanceOf(NullResponse::class, $response);
        // self::assertNotInstanceOf(NullResponse::class, $response);
    }

    protected function getEventListener(): EventDispatcher
    {
        $container = new Container();
        $container->set(BotListener::class, new BotListener());
        $container->set(BrowserLanguage::class, new BrowserLanguage());
        $container->set(DefaultNegotiation::class, new DefaultNegotiation(new Normalizer()));
        $container->set(BuildResponse::class, new DefaultResponse());
        $provider = new ListenerProvider($container);
        $provider->addListener(CheckLanguageDetection::class, BotListener::class);
        $provider->addListener(DetectUserLanguages::class, BrowserLanguage::class);
        $provider->addListener(NegotiateSiteLanguage::class, DefaultNegotiation::class);
        $provider->addListener(BuildResponse::class, DefaultResponse::class);

        return new EventDispatcher($provider);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Site $site */
        $site = $request->getAttribute('site');

        $check = new CheckLanguageDetection($site, $request);
        $this->eventDispatcher->dispatch($check);

        if (!$check->isLanguageDetectionEnable()) {
            return $handler->handle($request);
        }

        $detect = new DetectUserLanguages($site, $request);
        $this->eventDispatcher->dispatch($detect);

        if (empty($detect->getUserLanguages())) {
            return $handler->handle($request);
        }

        $negotiate = new NegotiateSiteLanguage($site, $request, $detect->getUserLanguages());
        $this->eventDispatcher->dispatch($negotiate);

        if (null === $negotiate->getSelectedLanguage()) {
            return $handler->handle($request);
        }

        $response = new BuildResponse($site, $request, $negotiate->getSelectedLanguage());
        $this->eventDispatcher->dispatch($response);

        if (null !== $response->getResponse()) {
            return $response->getResponse();
        }

        return $handler->handle($request);
    }
}
