<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Negotiation;

use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent;
use Lochmueller\LanguageDetection\Service\GlobalConfigurationService;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * Handle a fallback for a request without a matching site (NullSite), if no language of the
 * other sites matches. Without this fallback the regular 404 handling of TYPO3 is used.
 */
class NullSiteFallbackNegotiation
{
    public function __construct(
        protected GlobalConfigurationService $globalConfigurationService,
        protected SiteFinder $siteFinder
    ) {}

    public function __invoke(NegotiateSiteLanguageEvent $event): void
    {
        if (!($event->getSite() instanceof NullSite)) {
            return;
        }

        $configuration = $this->globalConfigurationService->getConfiguration();
        if (!$configuration->isEnableNullSiteDetection() || $configuration->getNullSiteFallbackSite() === '') {
            return;
        }

        try {
            $site = $this->siteFinder->getSiteByIdentifier($configuration->getNullSiteFallbackSite());
        } catch (SiteNotFoundException) {
            return;
        }

        foreach ($site->getLanguages() as $siteLanguage) {
            if ($siteLanguage->getLanguageId() === $configuration->getNullSiteFallbackLanguageId()) {
                $event->setSelectedLanguage($siteLanguage);

                return;
            }
        }
    }
}
