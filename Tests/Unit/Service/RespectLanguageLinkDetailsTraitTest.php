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
        /** @var RespectLanguageLinkDetailsTrait $traitObject */
        $traitObject = $this->getObjectForTrait(RespectLanguageLinkDetailsTrait::class);

        self::assertEquals(['type' => 'wrong'], $traitObject->addLanguageParameterByDetection(['type' => 'wrong']));
    }

    /**
     * @covers \LD\LanguageDetection\Service\RespectLanguageLinkDetailsTrait
     */
    public function testTraitExecutionParameterGeneration(): void
    {
        $en = new SiteLanguage(0, 'en_GB', new Uri('/en/'), ['enabled' => true]);
        $de = new SiteLanguage(1, 'de_DE', new Uri('/en/'), ['enabled' => true]);

        $site = $this->createStub(Site::class);
        $site->method('getAllLanguages')->willReturn([$en, $de]);

        $siteFinder = $this->createStub(SiteFinder::class);
        $siteFinder->method('getSiteByPageId')
            ->willReturn($site)
        ;

        /** @var RespectLanguageLinkDetailsTrait $traitObject */
        $traitObject = $this->getObjectForTrait(RespectLanguageLinkDetailsTrait::class);

        $reflectionClass = new \ReflectionClass($traitObject);
        $propertyLanguageEventDispatcher = $reflectionClass->getProperty('languageEventDispatcher');
        $propertyLanguageEventDispatcher->setAccessible(true);
        $propertyLanguageEventDispatcher->setValue($traitObject, $this->getEventListener());

        $propertyLanguageSiteFinder = $reflectionClass->getProperty('languageSiteFinder');
        $propertyLanguageSiteFinder->setAccessible(true);
        $propertyLanguageSiteFinder->setValue($traitObject, $siteFinder);

        $serverRequest = new ServerRequest('https://ww.google.de', 'GET', 'php://input', ['accept-language' => 'de,de_DE']);

        $propertyLanguageRequest = $reflectionClass->getProperty('languageRequest');
        $propertyLanguageRequest->setAccessible(true);
        $propertyLanguageRequest->setValue($traitObject, $serverRequest);

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
}
