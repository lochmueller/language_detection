<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Service;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Collect the languages of all sites, that are valid redirect targets for a request
 * without a matching site (NullSite).
 */
class SiteCandidateService
{
    public function __construct(
        protected SiteFinder $siteFinder,
        protected GlobalConfigurationService $globalConfigurationService
    ) {}

    /**
     * @return SiteLanguage[]
     */
    public function getCandidateLanguages(ServerRequestInterface $request): array
    {
        $configuration = $this->globalConfigurationService->getConfiguration();
        if (!$configuration->isEnableNullSiteDetection()) {
            // The JSON and link handler do not run the CheckLanguageDetectionEvent, so the
            // feature has to be checked here again.
            return [];
        }

        $allowedSites = GeneralUtility::trimExplode(',', $configuration->getNullSiteAllowedSites(), true);
        $requestHost = $request->getUri()->getHost();

        $languages = [];
        foreach ($this->siteFinder->getAllSites() as $site) {
            if ($allowedSites !== [] && !\in_array($site->getIdentifier(), $allowedSites, true)) {
                continue;
            }

            foreach ($this->getDetectableLanguages($site) as $siteLanguage) {
                if ($configuration->isNullSiteLimitToRequestHost() && !$this->matchHost($siteLanguage, $requestHost)) {
                    continue;
                }

                $languages[] = $siteLanguage;
            }
        }

        return $languages;
    }

    /**
     * @return SiteLanguage[]
     */
    protected function getDetectableLanguages(Site $site): array
    {
        $languages = [];
        foreach ($site->getLanguages() as $siteLanguage) {
            $configuration = $siteLanguage->toArray();
            if (!$siteLanguage->enabled() || ($configuration['excludeFromLanguageDetection'] ?? false) === true) {
                continue;
            }

            $languages[] = $siteLanguage;
        }

        return $languages;
    }

    /**
     * A redirect to another host is only allowed, if it is explicitly configured. Otherwise a
     * catch all vHost would send the visitor to a complete different domain.
     */
    protected function matchHost(SiteLanguage $siteLanguage, string $requestHost): bool
    {
        $languageHost = $siteLanguage->getBase()->getHost();

        return $languageHost === '' || strtolower($languageHost) === strtolower($requestHost);
    }
}
