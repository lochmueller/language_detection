<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Service\GlobalConfigurationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\GlobalConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\GlobalConfigurationService::class)]
class GlobalConfigurationServiceTest extends AbstractUnitTest
{
    public function testDefaultsWithoutExtensionConfiguration(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][GlobalConfigurationService::EXTENSION_KEY]);
        $service = new GlobalConfigurationService(new ExtensionConfiguration());

        $configuration = $service->getConfiguration();

        self::assertFalse($configuration->isEnableNullSiteDetection());
        self::assertTrue($configuration->isNullSiteLimitToRequestHost());
        self::assertSame('', $configuration->getNullSiteAllowedSites());
        self::assertSame('', $configuration->getNullSiteFallbackSite());
        self::assertSame(0, $configuration->getNullSiteFallbackLanguageId());
        self::assertSame([], $service->getSiteConfigurationDefaults());
    }

    public function testNullSiteConfiguration(): void
    {
        $service = $this->getGlobalConfigurationService([
            'enableNullSiteDetection' => '1',
            'nullSiteLimitToRequestHost' => '0',
            'nullSiteAllowedSites' => 'de,ch',
            'nullSiteFallbackSite' => 'de',
            'nullSiteFallbackLanguageId' => '2',
        ]);

        $configuration = $service->getConfiguration();

        self::assertTrue($configuration->isEnableNullSiteDetection());
        self::assertFalse($configuration->isNullSiteLimitToRequestHost());
        self::assertSame('de,ch', $configuration->getNullSiteAllowedSites());
        self::assertSame('de', $configuration->getNullSiteFallbackSite());
        self::assertSame(2, $configuration->getNullSiteFallbackLanguageId());
    }

    public function testSiteConfigurationDefaultsSkipGlobalOnlyAndEmptyOptions(): void
    {
        $service = $this->getGlobalConfigurationService([
            'enableNullSiteDetection' => '1',
            'languageDetectionMaxMindLicenseKey' => 'the-key',
            'languageDetectionMaxMindDatabasePath' => '',
            'allowAllPaths' => '0',
        ]);

        self::assertSame([
            'languageDetectionMaxMindLicenseKey' => 'the-key',
            'allowAllPaths' => '0',
        ], $service->getSiteConfigurationDefaults());
    }
}
