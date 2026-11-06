<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\GlobalConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\GlobalConfigurationService::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\SiteConfigurationService::class)]
class SiteConfigurationServiceTest extends AbstractUnitTest
{
    public function testCreationOfConfigurationDto(): void
    {
        $service = $this->getSiteConfigurationService();
        $config = $service->getConfiguration(self::createStub(SiteInterface::class));

        self::assertEquals(307, $config->getRedirectHttpStatusCode());
    }

    public function testGlobalConfigurationIsUsedForNonSiteObjects(): void
    {
        $service = $this->getSiteConfigurationService([
            'languageDetectionMaxMindLicenseKey' => 'global-key',
            'redirectHttpStatusCode' => '303',
        ]);
        $config = $service->getConfiguration(self::createStub(SiteInterface::class));

        self::assertEquals('global-key', $config->getMaxMindLicenseKey());
        self::assertEquals(303, $config->getRedirectHttpStatusCode());
    }

    public function testGlobalConfigurationIsUsedAsFallbackForMissingSiteOptions(): void
    {
        $site = self::createStub(Site::class);
        $site->method('getConfiguration')->willReturn(['redirectHttpStatusCode' => 302]);

        $service = $this->getSiteConfigurationService([
            'languageDetectionMaxMindLicenseKey' => 'global-key',
            'redirectHttpStatusCode' => '303',
        ]);
        $config = $service->getConfiguration($site);

        self::assertEquals('global-key', $config->getMaxMindLicenseKey());
        self::assertEquals(302, $config->getRedirectHttpStatusCode());
    }

    public function testEmptySiteOptionDoesNotOverwriteTheGlobalConfiguration(): void
    {
        $site = self::createStub(Site::class);
        $site->method('getConfiguration')->willReturn(['languageDetectionMaxMindLicenseKey' => '']);

        $service = $this->getSiteConfigurationService(['languageDetectionMaxMindLicenseKey' => 'global-key']);
        $config = $service->getConfiguration($site);

        self::assertEquals('global-key', $config->getMaxMindLicenseKey());
    }

    public function testFalseSiteOptionOverwritesTheGlobalConfiguration(): void
    {
        $site = self::createStub(Site::class);
        $site->method('getConfiguration')->willReturn(['enableLanguageDetection' => false]);

        $service = $this->getSiteConfigurationService(['enableLanguageDetection' => '1']);
        $config = $service->getConfiguration($site);

        self::assertFalse($config->isEnableLanguageDetection());
    }

    public function testSiteCanIgnoreTheGlobalConfiguration(): void
    {
        $site = self::createStub(Site::class);
        $site->method('getConfiguration')->willReturn(['ignoreGlobalLanguageDetectionConfiguration' => true]);

        $service = $this->getSiteConfigurationService([
            'languageDetectionMaxMindLicenseKey' => 'global-key',
            'redirectHttpStatusCode' => '303',
        ]);
        $config = $service->getConfiguration($site);

        self::assertEquals('', $config->getMaxMindLicenseKey());
        self::assertEquals(307, $config->getRedirectHttpStatusCode());
    }
}
