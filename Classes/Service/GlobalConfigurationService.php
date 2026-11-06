<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Service;

use Lochmueller\LanguageDetection\Domain\Model\Dto\GlobalConfiguration;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * Access the extension wide configuration (ext_conf_template.txt). The values are used as
 * fallback for the site configuration and for the configuration options, that are not part
 * of a single site (e.g. the NullSite handling).
 */
class GlobalConfigurationService
{
    public const EXTENSION_KEY = 'language_detection';

    /**
     * Configuration options that are not mirrored to the site configuration.
     *
     * @var string[]
     */
    protected const GLOBAL_ONLY_OPTIONS = [
        'enableNullSiteDetection',
        'nullSiteLimitToRequestHost',
        'nullSiteAllowedSites',
        'nullSiteFallbackSite',
        'nullSiteFallbackLanguageId',
    ];

    /**
     * @var array<string, mixed>|null
     */
    protected ?array $runtimeCache = null;

    public function __construct(protected ExtensionConfiguration $extensionConfiguration) {}

    public function getConfiguration(): GlobalConfiguration
    {
        $configuration = $this->getRawConfiguration();

        return new GlobalConfiguration(
            (bool)($configuration['enableNullSiteDetection'] ?? false),
            !\array_key_exists('nullSiteLimitToRequestHost', $configuration) || (bool)$configuration['nullSiteLimitToRequestHost'],
            (string)($configuration['nullSiteAllowedSites'] ?? ''),
            (string)($configuration['nullSiteFallbackSite'] ?? ''),
            (int)($configuration['nullSiteFallbackLanguageId'] ?? 0),
        );
    }

    /**
     * Global default values for the options of a single site configuration.
     *
     * @return array<string, mixed>
     */
    public function getSiteConfigurationDefaults(): array
    {
        $configuration = $this->getRawConfiguration();
        foreach (self::GLOBAL_ONLY_OPTIONS as $option) {
            unset($configuration[$option]);
        }

        return $this->removeEmptyValues($configuration);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getRawConfiguration(): array
    {
        if ($this->runtimeCache !== null) {
            return $this->runtimeCache;
        }

        if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][self::EXTENSION_KEY])) {
            // The extension configuration is not available (yet). Work with the defaults and
            // skip the (expensive) auto synchronization of the ExtensionConfiguration API.
            return $this->runtimeCache = [];
        }

        try {
            $configuration = $this->extensionConfiguration->get(self::EXTENSION_KEY);
        } catch (\Exception) {
            $configuration = [];
        }

        return $this->runtimeCache = \is_array($configuration) ? $configuration : [];
    }

    /**
     * An empty value in the extension configuration means "not configured", so the default
     * values of the SiteConfiguration DTO should win.
     *
     * @param array<string, mixed> $configuration
     * @return array<string, mixed>
     */
    protected function removeEmptyValues(array $configuration): array
    {
        return array_filter($configuration, fn($value): bool => $value !== '' && $value !== null);
    }
}
