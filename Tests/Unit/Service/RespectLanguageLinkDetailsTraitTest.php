<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Service;

use LD\LanguageDetection\Detect\BrowserLanguage;
use LD\LanguageDetection\Event\DetectUserLanguages;
use LD\LanguageDetection\Event\NegotiateSiteLanguage;
use LD\LanguageDetection\Negotiation\DefaultNegotiation;
use LD\LanguageDetection\Service\Normalizer;
use LD\LanguageDetection\Service\RespectLanguageLinkDetailsTrait;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\Container;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * @internal
 * @coversNothing
 */
class RespectLanguageLinkDetailsTraitTest extends AbstractTest
{
    /**
     * @covers \LD\LanguageDetection\Service\RespectLanguageLinkDetailsTrait
     */
    public function testTraitExecutionWithWrongType(): void
    {
        $traitObject = $this->createTraitMock();

        self::assertEquals(['type' => 'wrong'], $traitObject->addLanguageParameterByDetection(['type' => 'wrong']));
    }

    /**
     * @covers \LD\LanguageDetection\Check\EnableListener
     * @covers \LD\LanguageDetection\Detect\BrowserLanguage
     * @covers \LD\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     * @covers \LD\LanguageDetection\Event\DetectUserLanguages
     * @covers \LD\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \LD\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \LD\LanguageDetection\Service\Normalizer
     * @covers \LD\LanguageDetection\Service\RespectLanguageLinkDetailsTrait
     * @covers \LD\LanguageDetection\Service\SiteConfigurationService
     */
    public function testTraitExecutionWithDisabledLanguageDetection(): void
    {
        $siteFinder = $this->createStub(SiteFinder::class);
        $siteFinder->method('getSiteByPageId')
            ->willReturn(new Site('dummy', 1, ['enableLanguageDetection' => false]))
        ;

        $serverRequest = new ServerRequest('https://ww.google.de', 'GET', 'php://input', ['accept-language' => 'de,de_DE', 'user-agent' => 'google']);

        $traitObject = $this->createTraitMock($this->createMock(EventDispatcher::class), $siteFinder, $serverRequest);

        $configuration = [
            'type' => LinkService::TYPE_PAGE,
            'pageuid' => 5,
        ];

        self::assertEquals($configuration, $traitObject->addLanguageParameterByDetection($configuration));
    }

    /**
     * @covers \LD\LanguageDetection\Check\EnableListener
     * @covers \LD\LanguageDetection\Detect\BrowserLanguage
     * @covers \LD\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     * @covers \LD\LanguageDetection\Event\DetectUserLanguages
     * @covers \LD\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \LD\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \LD\LanguageDetection\Service\Normalizer
     * @covers \LD\LanguageDetection\Service\RespectLanguageLinkDetailsTrait
     * @covers \LD\LanguageDetection\Service\SiteConfigurationService
     */
    public function testTraitExecutionWithNoUserLanguages(): void
    {
        $site = $this->createStub(Site::class);

        $siteFinder = $this->createStub(SiteFinder::class);
        $siteFinder->method('getSiteByPageId')
            ->willReturn($site)
        ;

        $serverRequest = new ServerRequest('https://ww.google.de', 'GET', 'php://input', ['accept-language' => 'de,de_DE']);

        $traitObject = $this->createTraitMock($this->getEventListenerWithoutUserLanguages(), $siteFinder, $serverRequest);

        $configuration = [
            'type' => LinkService::TYPE_PAGE,
            'pageuid' => 5,
        ];

        self::assertEquals($configuration, $traitObject->addLanguageParameterByDetection($configuration));
    }

    /**
     * @covers \LD\LanguageDetection\Check\EnableListener
     * @covers \LD\LanguageDetection\Detect\BrowserLanguage
     * @covers \LD\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     * @covers \LD\LanguageDetection\Event\DetectUserLanguages
     * @covers \LD\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \LD\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \LD\LanguageDetection\Service\Normalizer
     * @covers \LD\LanguageDetection\Service\RespectLanguageLinkDetailsTrait
     * @covers \LD\LanguageDetection\Service\SiteConfigurationService
     */
    public function testTraitExecutionParameterGeneration(): void
    {
        $en = new SiteLanguage(0, 'en_GB', new Uri('/en/'), ['enabled' => true]);
        $de = new SiteLanguage(1, 'de_DE', new Uri('/en/'), ['enabled' => true]);

        $site = $this->createStub(Site::class);
        $site->method('getLanguages')->willReturn([$en, $de]);

        $siteFinder = $this->createStub(SiteFinder::class);
        $siteFinder->method('getSiteByPageId')
            ->willReturn($site)
        ;

        $serverRequest = new ServerRequest('https://ww.google.de', 'GET', 'php://input', ['accept-language' => 'de,de_DE']);

        $traitObject = $this->createTraitMock($this->getEventListener(), $siteFinder, $serverRequest);

        $configuration = [
            'type' => LinkService::TYPE_PAGE,
            'pageuid' => 5,
        ];

        self::assertEquals([
            'type' => LinkService::TYPE_PAGE,
            'pageuid' => 5,
            'parameters' => 'L=1',
        ], $traitObject->addLanguageParameterByDetection($configuration));
    }

    protected function getEventListener(): EventDispatcher
    {
        $container = new Container();
        $container->set(BrowserLanguage::class, new BrowserLanguage());
        $container->set(DefaultNegotiation::class, new DefaultNegotiation(new Normalizer()));
        $provider = new ListenerProvider($container);
        $provider->addListener(DetectUserLanguages::class, BrowserLanguage::class);
        $provider->addListener(NegotiateSiteLanguage::class, DefaultNegotiation::class);

        return new EventDispatcher($provider);
    }

    protected function getEventListenerWithoutUserLanguages(): EventDispatcher
    {
        $container = new Container();
        $provider = new ListenerProvider($container);

        return new EventDispatcher($provider);
    }

    protected function createTraitMock(?EventDispatcherInterface $languageEventDispatcher = null, ?SiteFinder $languageSiteFinder = null, ?ServerRequest $serverRequest = null): object
    {
        $traitObject = $this->getObjectForTrait(RespectLanguageLinkDetailsTrait::class);
        $reflectionClass = new \ReflectionClass($traitObject);

        if (null !== $languageEventDispatcher) {
            $propertyLanguageEventDispatcher = $reflectionClass->getProperty('languageEventDispatcher');
            $propertyLanguageEventDispatcher->setAccessible(true);
            $propertyLanguageEventDispatcher->setValue($traitObject, $languageEventDispatcher);
        }

        if (null !== $languageSiteFinder) {
            $propertyLanguageSiteFinder = $reflectionClass->getProperty('languageSiteFinder');
            $propertyLanguageSiteFinder->setAccessible(true);
            $propertyLanguageSiteFinder->setValue($traitObject, $languageSiteFinder);
        }

        if (null !== $serverRequest) {
            $propertyLanguageRequest = $reflectionClass->getProperty('languageRequest');
            $propertyLanguageRequest->setAccessible(true);
            $propertyLanguageRequest->setValue($traitObject, $serverRequest);
        }

        return $traitObject;
    }
}
