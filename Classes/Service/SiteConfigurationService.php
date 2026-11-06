<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Service;

use Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SiteConfigurationService
{
    /**
     * Site configuration option to skip the global extension configuration for a single site.
     */
    public const IGNORE_GLOBAL_CONFIGURATION = 'ignoreGlobalLanguageDetectionConfiguration';

    public function __construct(protected ?GlobalConfigurationService $globalConfigurationService = null) {}

    public function getConfiguration(SiteInterface $site): SiteConfiguration
    {
        $config = $this->mergeWithGlobalConfiguration($site instanceof Site ? $site->getConfiguration() : []);

        return new SiteConfiguration(
            !\array_key_exists('enableLanguageDetection', $config) || (bool)$config['enableLanguageDetection'],
            (bool)($config['disableRedirectWithBackendSession'] ?? false),
            (string)($config['addIpLocationToBrowserLanguage'] ?? ''),
            (bool)($config['allowAllPaths'] ?? false),
            (int)($config['redirectHttpStatusCode'] ?? 307),
            (string)($config['forwardRedirectParameters'] ?? ''),
            (int)($config['fallbackDetectionLanguage'] ?? 0),
            (string)($config['languageDetectionMaxMindDatabasePath'] ?? ''),
            (int)($config['languageDetectionMaxMindAccountId'] ?? ''),
            (string)($config['languageDetectionMaxMindLicenseKey'] ?? ''),
            (string)($config['languageDetectionMaxMindMode'] ?? 'After'),
            (int)($config['ipAddressPrecision'] ?? 4),
        );
    }

    /**
     * Use the global extension configuration as default and let every option, that is really
     * set in the site configuration, win. An empty string (or NULL) in the site configuration
     * means "not set", because the site configuration module writes empty fields to the YAML file.
     *
     * @param array<string, mixed> $siteConfiguration
     * @return array<string, mixed>
     */
    protected function mergeWithGlobalConfiguration(array $siteConfiguration): array
    {
        if ((bool)($siteConfiguration[self::IGNORE_GLOBAL_CONFIGURATION] ?? false)) {
            return $siteConfiguration;
        }

        $config = $this->getGlobalConfigurationService()->getSiteConfigurationDefaults();
        foreach ($siteConfiguration as $key => $value) {
            if ($value === '' || $value === null) {
                continue;
            }

            $config[$key] = $value;
        }

        return $config;
    }

    protected function getGlobalConfigurationService(): GlobalConfigurationService
    {
        return $this->globalConfigurationService ??= new GlobalConfigurationService(GeneralUtility::makeInstance(ExtensionConfiguration::class));
    }
}
