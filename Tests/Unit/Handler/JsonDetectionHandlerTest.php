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
use Lochmueller\LanguageDetection\Service\IpAnonymizationService;
use Lochmueller\LanguageDetection\Service\LanguageService;
use Lochmueller\LanguageDetection\Service\LocaleCollectionSortService;
use Lochmueller\LanguageDetection\Service\Normalizer;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DependencyInjection\Container;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Check\BotAgentCheck::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\AbstractHandler::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\JsonDetectionHandler::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Response\DefaultResponse::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Detect\MaxMindDetect::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\LanguageService::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\LocaleCollectionSortService::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\Normalizer::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\BuildResponseEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\SiteConfigurationService::class)]
class JsonDetectionHandlerTest extends AbstractHandlerTest
{
    public function testInvalidPath(): void
    {
        $this->expectException(DisableLanguageDetectionException::class);

        $serverRequest = new ServerRequest('https://www.dummy.de/', null, 'php://input', ['accept-language' => 'de,de_DE']);
        $handler = new JsonDetectionHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }
    /**
     * @see https://github.com/lochmueller/language_detection/issues/34
     */
    public function testIssue34ResultOfHeaderAndMaxmindUsMatchLanguages(): void
    {
        $container = new Container();
        $container->set(BotAgentCheck::class, new BotAgentCheck());
        $container->set(BrowserLanguageDetect::class, new BrowserLanguageDetect());
        $container->set('staticMaxMind', new class (new IpAnonymizationService(), new LanguageService(), $this->getSiteConfigurationService(), new LocaleCollectionSortService()) extends MaxMindDetect {
            public function __invoke(DetectUserLanguagesEvent $event): void
            {
                $fakeMaxMindResult = 'us';
                $mode = 'before';
                $locale = $this->languageService->getLanguageByCountry($fakeMaxMindResult) . '_' . $fakeMaxMindResult;
                $event->setUserLanguages($this->localeCollectionSortService->addLocaleByMode($event->getUserLanguages(), new LocaleValueObject($locale), $mode));
            }
        });

        $container->set(DefaultNegotiation::class, new DefaultNegotiation(new Normalizer()));
        $container->set(DefaultResponse::class, new DefaultResponse($this->getSiteConfigurationService()));
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

    public function testBreakAfterDetectUserLanguagesByMissingLanguages(): void
    {
        $this->expectException(NoUserLanguagesException::class);

        $serverRequest = new ServerRequest('https://www.dummy.de/language.json', null, 'php://input', ['accept-language' => '']);
        $serverRequest = $serverRequest->withAttribute('site', new Site('dummy', 1, []));

        $handler = new JsonDetectionHandler($this->getSmallEventListenerStack());
        $handler->handle($serverRequest);
    }

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
