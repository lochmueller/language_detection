<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Handler;

use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use Lochmueller\LanguageDetection\Handler\LinkLanguageHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Handler\AbstractHandler::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\LinkLanguageHandler::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Check\BotAgentCheck::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Check\EnableCheck::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Response\DefaultResponse::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\SiteConfigurationService::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\Normalizer::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\BuildResponseEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\Exception\NoResponseException::class)]
class LinkLanguageHandlerTest extends AbstractHandlerTest
{
    public function testCallLinkHandlerWithoutSite(): void
    {
        $this->expectExceptionCode(1_637_813_123);

        $handler = new LinkLanguageHandler(self::createStub(EventDispatcherInterface::class));
        $handler->handle(new ServerRequest());
    }

    public function testBreakAfterCheckLanguageDetectionByAddingBotAgent(): void
    {
        $this->expectException(DisableLanguageDetectionException::class);
        $this->expectExceptionCode(1_236_781);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['user-agent' => 'AdsBot-Google']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, ['enableLanguageDetection' => false]));

        $handler = new LinkLanguageHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    public function testBreakAfterDetectUserLanguagesByMissingLanguages(): void
    {
        $this->expectException(NoUserLanguagesException::class);

        $serverRequest = new ServerRequest();
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $handler = new LinkLanguageHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    public function testBreakAfterNegotiateSiteLanguageByNotFoundTargetLanguage(): void
    {
        $this->expectException(NoSelectedLanguageException::class);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['accept-language' => 'de,de_DE']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $handler = new LinkLanguageHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

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
