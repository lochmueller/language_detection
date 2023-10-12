<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Handler;

use Lochmueller\LanguageDetection\Check\BotAgentCheck;
use Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect;
use Lochmueller\LanguageDetection\Detect\MaxMindDetect;
use Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject;
use Lochmueller\LanguageDetection\Event\BuildResponseEvent;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent;
use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use Lochmueller\LanguageDetection\Handler\JsonDetectionHandler;
use Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation;
use Lochmueller\LanguageDetection\Response\DefaultResponse;
use Lochmueller\LanguageDetection\Service\LanguageService;
use Lochmueller\LanguageDetection\Service\LocaleCollectionSortService;
use Lochmueller\LanguageDetection\Service\Normalizer;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DependencyInjection\Container;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 *
 * @coversNothing
 */
class JsonDetectionHandlerTest extends AbstractHandlerTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Check\BotAgentCheck
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent
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
     * @covers \Lochmueller\LanguageDetection\Check\BotAgentCheck
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent
     * @covers \Lochmueller\LanguageDetection\Handler\AbstractHandler
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException
     * @covers \Lochmueller\LanguageDetection\Handler\JsonDetectionHandler
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @see https://github.com/lochmueller/language_detection/issues/34
     */
    public function testIssue34ResultOfHeaderAndMaxmindUsMatchLanguages(): void
    {
        $container = new Container();
        $container->set(BotAgentCheck::class, new BotAgentCheck());
        $container->set(BrowserLanguageDetect::class, new BrowserLanguageDetect());
        $container->set('staticMaxMind', new class (new LanguageService(), new SiteConfigurationService(), new LocaleCollectionSortService()) extends MaxMindDetect {
            public function __invoke(DetectUserLanguagesEvent $event): void
            {
                $fakeMaxMindResult = 'us';
                $mode = 'Before';
                $locale = $this->languageService->getLanguageByCountry($fakeMaxMindResult) . '_' . $fakeMaxMindResult;
                $event->setUserLanguages($this->localeCollectionSortService->addLocaleByMode($event->getUserLanguages(), new LocaleValueObject($locale), $mode));
            }
        });

        $container->set(DefaultNegotiation::class, new DefaultNegotiation(new Normalizer()));
        $container->set(DefaultResponse::class, new DefaultResponse(new SiteConfigurationService()));
        $provider = new ListenerProvider($container);
        $provider->addListener(CheckLanguageDetectionEvent::class, BotAgentCheck::class);
        $provider->addListener(DetectUserLanguagesEvent::class, BrowserLanguageDetect::class);
        $provider->addListener(DetectUserLanguagesEvent::class, 'staticMaxMind');
        $provider->addListener(NegotiateSiteLanguageEvent::class, DefaultNegotiation::class);
        $provider->addListener(BuildResponseEvent::class, DefaultResponse::class);

        $serverRequest = new ServerRequest('https://www.dummy.de/language.json', null, 'php://input', ['accept-language' => 'en-US,en;q=0.9']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, [
            'languages' => [
                [
                    'languageId' => 1,
                    'base' => '/de-de/',
                    'locale' => 'de_DE',
                ],
                [
                    'languageId' => 2,
                    'base' => '/en-us/',
                    'locale' => 'en_US',
                ],
                [
                    'languageId' => 3,
                    'base' => '/en-gb/',
                    'locale' => 'en_GB',
                ],
                [
                    'languageId' => 4,
                    'base' => '/fr-fr/',
                    'locale' => 'fr_FR',
                ],
            ],
        ]));
        $handler = new JsonDetectionHandler(new EventDispatcher($provider));
        $result = $handler->handle($serverRequest);

        $json = \json_decode($result->getBody()->getContents());

        self::assertEquals('en_US', $json->locale);
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\BotAgentCheck
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent
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
     * @covers \Lochmueller\LanguageDetection\Check\BotAgentCheck
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponseEvent
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent
     * @covers \Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent
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
        /* @var \stdClass $content */
        self::assertEquals(3, $content->languageId);
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\BotAgentCheck
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponseEvent
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent
     * @covers \Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent
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
        /* @var \stdClass $content */
        self::assertEquals(1, $content->languageId);
    }
}
