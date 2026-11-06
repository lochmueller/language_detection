<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Service\SiteCandidateService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\GlobalConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\GlobalConfigurationService::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\SiteCandidateService::class)]
class SiteCandidateServiceTest extends AbstractUnitTest
{
    public function testCollectAllLanguagesOfAllSites(): void
    {
        $service = new SiteCandidateService($this->getSiteFinder(), $this->getGlobalConfigurationService(['enableNullSiteDetection' => '1']));
        $languages = $service->getCandidateLanguages(new ServerRequest('https://www.example.com/'));

        self::assertSame(
            ['https://www.example.com/de/', 'https://www.example.com/ch/de/', 'https://www.example.com/ch/fr/'],
            $this->getBases($languages)
        );
    }

    public function testSkipLanguagesOfOtherHosts(): void
    {
        $service = new SiteCandidateService($this->getSiteFinder(), $this->getGlobalConfigurationService(['enableNullSiteDetection' => '1']));
        $languages = $service->getCandidateLanguages(new ServerRequest('https://www.other.com/'));

        self::assertSame([], $this->getBases($languages));
    }

    public function testAllowCrossDomainLanguages(): void
    {
        $globalConfiguration = $this->getGlobalConfigurationService(['enableNullSiteDetection' => '1', 'nullSiteLimitToRequestHost' => '0']);
        $service = new SiteCandidateService($this->getSiteFinder(), $globalConfiguration);
        $languages = $service->getCandidateLanguages(new ServerRequest('https://www.other.com/'));

        self::assertCount(3, $languages);
    }

    public function testRestrictCandidatesToTheAllowedSites(): void
    {
        $globalConfiguration = $this->getGlobalConfigurationService(['enableNullSiteDetection' => '1', 'nullSiteAllowedSites' => 'ch']);
        $service = new SiteCandidateService($this->getSiteFinder(), $globalConfiguration);
        $languages = $service->getCandidateLanguages(new ServerRequest('https://www.example.com/'));

        self::assertSame(
            ['https://www.example.com/ch/de/', 'https://www.example.com/ch/fr/'],
            $this->getBases($languages)
        );
    }

    public function testSkipDisabledAndExcludedLanguages(): void
    {
        $site = new Site('de', 1, [
            'base' => 'https://www.example.com/de/',
            'languages' => [
                ['languageId' => 0, 'base' => '/', 'locale' => 'de_DE', 'enabled' => false],
                ['languageId' => 1, 'base' => '/en/', 'locale' => 'en_GB', 'excludeFromLanguageDetection' => true],
                ['languageId' => 2, 'base' => '/fr/', 'locale' => 'fr_FR'],
            ],
        ]);

        $siteFinder = self::createStub(SiteFinder::class);
        $siteFinder->method('getAllSites')->willReturn(['de' => $site]);

        $service = new SiteCandidateService($siteFinder, $this->getGlobalConfigurationService(['enableNullSiteDetection' => '1']));
        $languages = $service->getCandidateLanguages(new ServerRequest('https://www.example.com/'));

        self::assertSame(['https://www.example.com/de/fr/'], $this->getBases($languages));
    }

    public function testNoCandidatesIfTheFeatureIsDisabled(): void
    {
        $service = new SiteCandidateService($this->getSiteFinder(), $this->getGlobalConfigurationService());
        $languages = $service->getCandidateLanguages(new ServerRequest('https://www.example.com/'));

        self::assertSame([], $languages);
    }

    protected function getSiteFinder(): SiteFinder
    {
        $de = new Site('de', 1, [
            'base' => 'https://www.example.com/de/',
            'languages' => [
                ['languageId' => 0, 'base' => '/', 'locale' => 'de_DE'],
            ],
        ]);
        $ch = new Site('ch', 2, [
            'base' => 'https://www.example.com/ch/',
            'languages' => [
                ['languageId' => 0, 'base' => '/de/', 'locale' => 'de_CH'],
                ['languageId' => 1, 'base' => '/fr/', 'locale' => 'fr_CH'],
            ],
        ]);

        $siteFinder = self::createStub(SiteFinder::class);
        $siteFinder->method('getAllSites')->willReturn(['de' => $de, 'ch' => $ch]);

        return $siteFinder;
    }

    /**
     * @param SiteLanguage[] $languages
     * @return string[]
     */
    protected function getBases(array $languages): array
    {
        return array_map(static fn(SiteLanguage $language): string => (string)$language->getBase(), $languages);
    }
}
