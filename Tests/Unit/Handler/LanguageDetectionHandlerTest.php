<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Handler;

use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Handler\Exception\NoResponseException;
use Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use Lochmueller\LanguageDetection\Handler\LanguageDetectionHandler;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 * @coversNothing
 */
class LanguageDetectionHandlerTest extends AbstractHandlerTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Check\BotAgentCheck
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException
     * @covers \Lochmueller\LanguageDetection\Handler\LanguageDetectionHandler
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     */
    public function testBreakAfterCheckLanguageDetectionByAddingBotAgent(): void
    {
        $this->expectException(DisableLanguageDetectionException::class);
        $this->expectExceptionCode(1_236_781);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['user-agent' => 'AdsBot-Google']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $handler = new LanguageDetectionHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\BotAgentCheck
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguages
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException
     * @covers \Lochmueller\LanguageDetection\Handler\LanguageDetectionHandler
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     */
    public function testBreakAfterDetectUserLanguagesByMissingLanguages(): void
    {
        $this->expectException(NoUserLanguagesException::class);

        $serverRequest = new ServerRequest();
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $handler = new LanguageDetectionHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\BotAgentCheck
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguages
     * @covers \Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException
     * @covers \Lochmueller\LanguageDetection\Handler\LanguageDetectionHandler
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\Normalizer
     */
    public function testBreakAfterNegotiateSiteLanguageByNotFoundTargetLanguage(): void
    {
        $this->expectException(NoSelectedLanguageException::class);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['accept-language' => 'de,de_DE']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $handler = new LanguageDetectionHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\BotAgentCheck
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponse
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguages
     * @covers \Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\NoResponseException
     * @covers \Lochmueller\LanguageDetection\Handler\LanguageDetectionHandler
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\Normalizer
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
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

    /**
     * @covers \Lochmueller\LanguageDetection\Check\BotAgentCheck
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponse
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguages
     * @covers \Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\LanguageDetectionHandler
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\Normalizer
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
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
