<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Middleware;

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
     * @covers \LD\LanguageDetection\Check\BotListener
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     * @covers \LD\LanguageDetection\Middleware\LanguageDetection
     * @covers \LD\LanguageDetection\Negotiation\DefaultNegotiation
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
     * @covers \LD\LanguageDetection\Check\BotListener
     * @covers \LD\LanguageDetection\Detect\BrowserLanguage
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     * @covers \LD\LanguageDetection\Event\DetectUserLanguages
     * @covers \LD\LanguageDetection\Middleware\LanguageDetection
     * @covers \LD\LanguageDetection\Negotiation\DefaultNegotiation
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
     * @covers \LD\LanguageDetection\Check\BotListener
     * @covers \LD\LanguageDetection\Detect\BrowserLanguage
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     * @covers \LD\LanguageDetection\Event\DetectUserLanguages
     * @covers \LD\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \LD\LanguageDetection\Middleware\LanguageDetection
     * @covers \LD\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \LD\LanguageDetection\Service\Normalizer
     */
    public function testBreakAfterNegotiateSiteLanguageByNotFoundTargetLanguage(): void
    {
        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new NullResponse());

        $serverRequest = new ServerRequest(null, null, 'php://input', ['accept-language' => 'de,de_DE']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $middleware = new LanguageDetection($this->getEventListener());
        $response = $middleware->process($serverRequest, $handler);

        self::assertInstanceOf(NullResponse::class, $response);
    }

    /**
     * @covers \LD\LanguageDetection\Check\BotListener
     * @covers \LD\LanguageDetection\Detect\BrowserLanguage
     * @covers \LD\LanguageDetection\Event\BuildResponse
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     * @covers \LD\LanguageDetection\Event\DetectUserLanguages
     * @covers \LD\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \LD\LanguageDetection\Middleware\LanguageDetection
     * @covers \LD\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \LD\LanguageDetection\Response\DefaultResponse
     * @covers \LD\LanguageDetection\Service\Normalizer
     */
    public function testBreakAfterBuildResponseByEmptyResponseBecauseOfSameUri(): void
    {
        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new NullResponse());

        $serverRequest = new ServerRequest('https://www.dummy.de/', null, 'php://input', ['accept-language' => 'de,de_DE']);
        $site = new Site('dummy', 1, [
            'base' => 'https://www.dummy.de/',
            'forwardRedirectParameters' => '',
            'languages' => [
                [
                    'languageId' => 1,
                    'base' => '/',
                    'locale' => 'de_DE',
                ],
                [
                    'languageId' => 2,
                    'base' => '/en/',
                    'locale' => 'en_GB',
                ],
            ],
        ]);

        $serverRequest = $serverRequest->withAttribute('site', $site);

        $middleware = new LanguageDetection($this->getEventListener());
        $response = $middleware->process($serverRequest, $handler);

        self::assertInstanceOf(NullResponse::class, $response);
    }

    /**
     * @covers \LD\LanguageDetection\Check\BotListener
     * @covers \LD\LanguageDetection\Detect\BrowserLanguage
     * @covers \LD\LanguageDetection\Event\BuildResponse
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     * @covers \LD\LanguageDetection\Event\DetectUserLanguages
     * @covers \LD\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \LD\LanguageDetection\Middleware\LanguageDetection
     * @covers \LD\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \LD\LanguageDetection\Response\DefaultResponse
     * @covers \LD\LanguageDetection\Service\Normalizer
     */
    public function testFullExecution(): void
    {
        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new NullResponse());

        $serverRequest = new ServerRequest('https://www.dummy.de/', null, 'php://input', ['accept-language' => 'de,de_DE']);
        $site = new Site('dummy', 1, [
            'base' => 'https://www.dummy.de/',
            'forwardRedirectParameters' => '',
            'languages' => [
                [
                    'languageId' => 1,
                    'base' => '/de/',
                    'locale' => 'de_DE',
                ],
                [
                    'languageId' => 2,
                    'base' => '/en/',
                    'locale' => 'en_GB',
                ],
            ],
        ]);

        $serverRequest = $serverRequest->withAttribute('site', $site);

        $middleware = new LanguageDetection($this->getEventListener());
        $response = $middleware->process($serverRequest, $handler);

        self::assertNotInstanceOf(NullResponse::class, $response);
    }

    protected function getEventListener(): EventDispatcher
    {
        $container = new Container();
        $container->set(BotListener::class, new BotListener());
        $container->set(BrowserLanguage::class, new BrowserLanguage());
        $container->set(DefaultNegotiation::class, new DefaultNegotiation(new Normalizer()));
        $container->set(DefaultResponse::class, new DefaultResponse());
        $provider = new ListenerProvider($container);
        $provider->addListener(CheckLanguageDetection::class, BotListener::class);
        $provider->addListener(DetectUserLanguages::class, BrowserLanguage::class);
        $provider->addListener(NegotiateSiteLanguage::class, DefaultNegotiation::class);
        $provider->addListener(BuildResponse::class, DefaultResponse::class);

        return new EventDispatcher($provider);
    }
}
