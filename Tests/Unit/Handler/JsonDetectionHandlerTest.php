<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Handler;

use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use Lochmueller\LanguageDetection\Handler\JsonDetectionHandler;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 * @coversNothing
 */
class JsonDetectionHandlerTest extends AbstractHandlerTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Check\BotListener
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguage
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguages
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException
     * @covers \Lochmueller\LanguageDetection\Handler\JsonDetectionHandler
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     */
    public function testInvalidPath(): void
    {
        $this->expectException(DisableLanguageDetectionException::class);

        $serverRequest = new ServerRequest('https://www.dummy.de/', null, 'php://input', ['accept-language' => 'de,de_DE']);
        $handler = new JsonDetectionHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\BotListener
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguage
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguages
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException
     * @covers \Lochmueller\LanguageDetection\Handler\JsonDetectionHandler
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     */
    public function testBreakAfterDetectUserLanguagesByMissingLanguages(): void
    {
        $this->expectException(NoUserLanguagesException::class);

        $serverRequest = new ServerRequest('https://www.dummy.de/language.json', null, 'php://input', ['accept-language' => '']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $handler = new JsonDetectionHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\BotListener
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguage
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponse
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguages
     * @covers \Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\JsonDetectionHandler
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\Normalizer
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testFullExecution(): void
    {
        $serverRequest = new ServerRequest('https://www.dummy.de/language.json', null, 'php://input', ['accept-language' => 'fr_FR,de,de_DE']);
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
                [
                    'languageId' => 3,
                    'base' => '/fr/',
                    'locale' => 'fr_FR',
                ],
            ],
        ]);

        $serverRequest = $serverRequest->withAttribute('site', $site);

        $handler = new JsonDetectionHandler($this->getSmallEventListenerStack());
        $response = $handler->handle($serverRequest);

        $content = json_decode($response->getBody()->getContents(), false, 512, \JSON_THROW_ON_ERROR);
        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertInstanceOf(\stdClass::class, $content);
        self::assertEquals(3, $content->languageId);
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\BotListener
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguage
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponse
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguages
     * @covers \Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\JsonDetectionHandler
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\Normalizer
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testDefaultLanguageExecution(): void
    {
        $serverRequest = new ServerRequest('https://www.dummy.de/language.json', null, 'php://input', ['accept-language' => 'fr']);
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

        $handler = new JsonDetectionHandler($this->getSmallEventListenerStack());
        $response = $handler->handle($serverRequest);

        $content = json_decode($response->getBody()->getContents(), false, 512, \JSON_THROW_ON_ERROR);
        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertInstanceOf(\stdClass::class, $content);
        self::assertEquals(1, $content->languageId);
    }
}
