<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Domain\Model\Dto;

/**
 * Extension wide configuration (ext_conf_template.txt), that is not part of a single site configuration.
 */
class GlobalConfiguration
{
    public function __construct(
        protected bool $enableNullSiteDetection,
        protected bool $nullSiteLimitToRequestHost,
        protected string $nullSiteAllowedSites,
        protected string $nullSiteFallbackSite,
        protected int $nullSiteFallbackLanguageId
    ) {}

    public function isEnableNullSiteDetection(): bool
    {
        return $this->enableNullSiteDetection;
    }

    public function isNullSiteLimitToRequestHost(): bool
    {
        return $this->nullSiteLimitToRequestHost;
    }

    public function getNullSiteAllowedSites(): string
    {
        return $this->nullSiteAllowedSites;
    }

    public function getNullSiteFallbackSite(): string
    {
        return $this->nullSiteFallbackSite;
    }

    public function getNullSiteFallbackLanguageId(): int
    {
        return $this->nullSiteFallbackLanguageId;
    }
}
