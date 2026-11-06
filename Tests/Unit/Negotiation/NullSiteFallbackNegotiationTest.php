<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Negotiation;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent;
use Lochmueller\LanguageDetection\Negotiation\NullSiteFallbackNegotiation;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\GlobalConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Negotiation\NullSiteFallbackNegotiation::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\GlobalConfigurationService::class)]
class NullSiteFallbackNegotiationTest extends AbstractUnitTest
{
    public function testRegularSiteIsNotTouched(): void
    {
        $event = $this->getEvent(self::createStub(Site::class));

        $negotiation = new NullSiteFallbackNegotiation(
            $this->getGlobalConfigurationService(['enableNullSiteDetection' => '1', 'nullSiteFallbackSite' => 'ch']),
            $this->getSiteFinder()
        );
        $negotiation($event);

        self::assertNull($event->getSelectedLanguage());
    }

    public function testNoFallbackIfTheFeatureIsDisabled(): void
    {
        $event = $this->getEvent(new NullSite());

        $negotiation = new NullSiteFallbackNegotiation(
            $this->getGlobalConfigurationService(['nullSiteFallbackSite' => 'ch']),
            $this->getSiteFinder()
        );
        $negotiation($event);

        self::assertNull($event->getSelectedLanguage());
    }

    public function testNoFallbackSiteConfigured(): void
    {
        $event = $this->getEvent(new NullSite());

        $negotiation = new NullSiteFallbackNegotiation($this->getGlobalConfigurationService(['enableNullSiteDetection' => '1']), $this->getSiteFinder());
        $negotiation($event);

        self::assertNull($event->getSelectedLanguage());
    }

    public function testUnknownFallbackSite(): void
    {
        $event = $this->getEvent(new NullSite());

        $siteFinder = self::createStub(SiteFinder::class);
        $siteFinder->method('getSiteByIdentifier')->willThrowException(new SiteNotFoundException('no site', 1_521_716_628));

        $negotiation = new NullSiteFallbackNegotiation(
            $this->getGlobalConfigurationService(['enableNullSiteDetection' => '1', 'nullSiteFallbackSite' => 'unknown']),
            $siteFinder
        );
        $negotiation($event);

        self::assertNull($event->getSelectedLanguage());
    }

    public function testFallbackToTheDefaultLanguageOfTheFallbackSite(): void
    {
        $event = $this->getEvent(new NullSite());

        $negotiation = new NullSiteFallbackNegotiation(
            $this->getGlobalConfigurationService(['enableNullSiteDetection' => '1', 'nullSiteFallbackSite' => 'ch']),
            $this->getSiteFinder()
        );
        $negotiation($event);

        $selectedLanguage = $event->getSelectedLanguage();
        self::assertInstanceOf(SiteLanguage::class, $selectedLanguage);
        self::assertEquals('https://www.example.com/ch/de/', (string)$selectedLanguage->getBase());
    }

    public function testFallbackToAConfiguredLanguageOfTheFallbackSite(): void
    {
        $event = $this->getEvent(new NullSite());

        $negotiation = new NullSiteFallbackNegotiation(
            $this->getGlobalConfigurationService(['enableNullSiteDetection' => '1', 'nullSiteFallbackSite' => 'ch', 'nullSiteFallbackLanguageId' => '1']),
            $this->getSiteFinder()
        );
        $negotiation($event);

        $selectedLanguage = $event->getSelectedLanguage();
        self::assertInstanceOf(SiteLanguage::class, $selectedLanguage);
        self::assertEquals('https://www.example.com/ch/fr/', (string)$selectedLanguage->getBase());
    }

    public function testUnknownFallbackLanguage(): void
    {
        $event = $this->getEvent(new NullSite());

        $negotiation = new NullSiteFallbackNegotiation(
            $this->getGlobalConfigurationService(['enableNullSiteDetection' => '1', 'nullSiteFallbackSite' => 'ch', 'nullSiteFallbackLanguageId' => '99']),
            $this->getSiteFinder()
        );
        $negotiation($event);

        self::assertNull($event->getSelectedLanguage());
    }

    protected function getEvent(Site|NullSite $site): NegotiateSiteLanguageEvent
    {
        return new NegotiateSiteLanguageEvent($site, self::createStub(ServerRequestInterface::class), LocaleCollection::fromArray(['es']));
    }

    protected function getSiteFinder(): SiteFinder
    {
        $ch = new Site('ch', 2, [
            'base' => 'https://www.example.com/ch/',
            'languages' => [
                ['languageId' => 0, 'base' => '/de/', 'locale' => 'de_CH'],
                ['languageId' => 1, 'base' => '/fr/', 'locale' => 'fr_CH'],
            ],
        ]);

        $siteFinder = self::createStub(SiteFinder::class);
        $siteFinder->method('getSiteByIdentifier')->willReturn($ch);

        return $siteFinder;
    }
}
