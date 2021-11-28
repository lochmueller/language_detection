<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Handler;

use LD\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use LD\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use LD\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use LD\LanguageDetection\Handler\LinkLanguageHandler;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 * @coversNothing
 */
class LinkLanguageHandlerTest extends AbstractHandlerTest
{
    /**
     * @covers \LD\LanguageDetection\Handler\AbstractHandler
     * @covers \LD\LanguageDetection\Handler\LinkLanguageHandler
     */
    public function testCallLinkHandlerWithoutSite(): void
    {
        $this->expectExceptionCode(1_637_813_123);

        $handler = new LinkLanguageHandler($this->createMock(EventDispatcherInterface::class));
        $handler->handle(new ServerRequest());
    }

    /**
     * @covers \LD\LanguageDetection\Check\BotListener
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     * @covers \LD\LanguageDetection\Handler\AbstractHandler
     * @covers \LD\LanguageDetection\Handler\Exception\DisableLanguageDetectionException
     * @covers \LD\LanguageDetection\Handler\LinkLanguageHandler
     * @covers \LD\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \LD\LanguageDetection\Response\DefaultResponse
     */
    public function testBreakAfterCheckLanguageDetectionByAddingBotAgent(): void
    {
        $this->expectException(DisableLanguageDetectionException::class);
        $this->expectExceptionCode(1_236_781);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['user-agent' => 'AdsBot-Google']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $handler = new LinkLanguageHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    /**
     * @covers \LD\LanguageDetection\Check\BotListener
     * @covers \LD\LanguageDetection\Detect\BrowserLanguage
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     * @covers \LD\LanguageDetection\Event\DetectUserLanguages
     * @covers \LD\LanguageDetection\Handler\AbstractHandler
     * @covers \LD\LanguageDetection\Handler\Exception\NoUserLanguagesException
     * @covers \LD\LanguageDetection\Handler\LinkLanguageHandler
     * @covers \LD\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \LD\LanguageDetection\Response\DefaultResponse
     */
    public function testBreakAfterDetectUserLanguagesByMissingLanguages(): void
    {
        $this->expectException(NoUserLanguagesException::class);

        $serverRequest = new ServerRequest();
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $handler = new LinkLanguageHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    /**
     * @covers \LD\LanguageDetection\Check\BotListener
     * @covers \LD\LanguageDetection\Detect\BrowserLanguage
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     * @covers \LD\LanguageDetection\Event\DetectUserLanguages
     * @covers \LD\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \LD\LanguageDetection\Handler\AbstractHandler
     * @covers \LD\LanguageDetection\Handler\Exception\NoSelectedLanguageException
     * @covers \LD\LanguageDetection\Handler\LinkLanguageHandler
     * @covers \LD\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \LD\LanguageDetection\Response\DefaultResponse
     * @covers \LD\LanguageDetection\Service\Normalizer
     */
    public function testBreakAfterNegotiateSiteLanguageByNotFoundTargetLanguage(): void
    {
        $this->expectException(NoSelectedLanguageException::class);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['accept-language' => 'de,de_DE']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $handler = new LinkLanguageHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    /**
     * @covers \LD\LanguageDetection\Check\BotListener
     * @covers \LD\LanguageDetection\Detect\BrowserLanguage
     * @covers \LD\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \LD\LanguageDetection\Event\BuildResponse
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     * @covers \LD\LanguageDetection\Event\DetectUserLanguages
     * @covers \LD\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \LD\LanguageDetection\Handler\AbstractHandler
     * @covers \LD\LanguageDetection\Handler\Exception\NoResponseException
     * @covers \LD\LanguageDetection\Handler\LinkLanguageHandler
     * @covers \LD\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \LD\LanguageDetection\Response\DefaultResponse
     * @covers \LD\LanguageDetection\Service\Normalizer
     * @covers \LD\LanguageDetection\Service\SiteConfigurationService
     */
    public function testReturnValidHeaderForLinkHandling(): void
    {
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

        $handler = new LinkLanguageHandler($this->getSmallEventListenerStack());
        $response = $handler->handle($serverRequest);
        self::assertTrue($response->hasHeader(LinkLanguageHandler::HEADER_NAME));
        self::assertEquals('1', $response->getHeaderLine(LinkLanguageHandler::HEADER_NAME));
    }
}
