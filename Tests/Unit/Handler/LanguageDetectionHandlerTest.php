<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Handler;

use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Handler\Exception\NoResponseException;
use Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use Lochmueller\LanguageDetection\Handler\LanguageDetectionHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Check\BotAgentCheck::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\AbstractHandler::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\LanguageDetectionHandler::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Response\DefaultResponse::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\Normalizer::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\BuildResponseEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\Exception\NoResponseException::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\SiteConfigurationService::class)]
class LanguageDetectionHandlerTest extends AbstractHandlerTest
{
    public function testBreakAfterCheckLanguageDetectionByAddingBotAgent(): void
    {
        $this->expectException(DisableLanguageDetectionException::class);
        $this->expectExceptionCode(1_236_781);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['user-agent' => 'AdsBot-Google']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $handler = new LanguageDetectionHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    public function testBreakAfterDetectUserLanguagesByMissingLanguages(): void
    {
        $this->expectException(NoUserLanguagesException::class);

        $serverRequest = new ServerRequest();
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $handler = new LanguageDetectionHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    public function testBreakAfterNegotiateSiteLanguageByNotFoundTargetLanguage(): void
    {
        $this->expectException(NoSelectedLanguageException::class);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['accept-language' => 'de,de_DE']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $handler = new LanguageDetectionHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    public function testBreakAfterBuildResponseByEmptyResponseBecauseOfSameUri(): void
    {
        $this->expectException(NoResponseException::class);

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

        $handler = new LanguageDetectionHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    public function testFullExecution(): void
    {
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

        $handler = new LanguageDetectionHandler($this->getSmallEventListenerStack());
        $response = $handler->handle($serverRequest);

        self::assertInstanceOf(ResponseInterface::class, $response);
    }
}
