<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Handler;

use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use Lochmueller\LanguageDetection\Handler\LinkLanguageHandler;
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
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\LinkLanguageHandler
     */
    public function testCallLinkHandlerWithoutSite(): void
    {
        $this->expectExceptionCode(1_637_813_123);

        $handler = new LinkLanguageHandler($this->createMock(EventDispatcherInterface::class));
        $handler->handle(new ServerRequest());
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\BotListener
     * @covers \Lochmueller\LanguageDetection\Check\EnableListener
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException
     * @covers \Lochmueller\LanguageDetection\Handler\LinkLanguageHandler
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testBreakAfterCheckLanguageDetectionByAddingBotAgent(): void
    {
        $this->expectException(DisableLanguageDetectionException::class);
        $this->expectExceptionCode(1_236_781);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['user-agent' => 'AdsBot-Google']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, ['enableLanguageDetection' => false]));

        $handler = new LinkLanguageHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\BotListener
     * @covers \Lochmueller\LanguageDetection\Check\EnableListener
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguage
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguages
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException
     * @covers \Lochmueller\LanguageDetection\Handler\LinkLanguageHandler
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
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
     * @covers \Lochmueller\LanguageDetection\Check\BotListener
     * @covers \Lochmueller\LanguageDetection\Check\EnableListener
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguage
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguages
     * @covers \Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException
     * @covers \Lochmueller\LanguageDetection\Handler\LinkLanguageHandler
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\Normalizer
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
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
     * @covers \Lochmueller\LanguageDetection\Check\BotListener
     * @covers \Lochmueller\LanguageDetection\Check\EnableListener
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguage
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponse
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguages
     * @covers \Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\NoResponseException
     * @covers \Lochmueller\LanguageDetection\Handler\LinkLanguageHandler
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\Normalizer
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
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
