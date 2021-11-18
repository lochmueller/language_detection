<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Negotiation;

use LD\LanguageDetection\Event\NegotiateSiteLanguage;
use LD\LanguageDetection\Service\SiteConfigurationService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

class FallbackNegotiation
{
    protected SiteConfigurationService $siteConfigurationService;

    public function __construct(SiteConfigurationService $siteConfigurationService)
    {
        $this->siteConfigurationService = $siteConfigurationService;
    }

    public function __invoke(NegotiateSiteLanguage $event): void
    {
        $site = $event->getSite();
        if (!($site instanceof Site)) {
            return;
        }
        $configuration = $this->siteConfigurationService->getConfiguration($site);

        $fallback = $configuration->getFallbackDetectionLanguage();
        foreach ($site->getAllLanguages() as $siteLanguage) {
            /** @var SiteLanguage $siteLanguage */
            if ($siteLanguage->getLanguageId() === $fallback) {
                $event->setSelectedLanguage($siteLanguage);

                return;
            }
        }
    }
}
