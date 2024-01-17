<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Negotiation;

use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use TYPO3\CMS\Core\Site\Entity\Site;

class FallbackNegotiation
{
    public function __construct(protected SiteConfigurationService $siteConfigurationService) {}

    public function __invoke(NegotiateSiteLanguageEvent $event): void
    {
        $site = $event->getSite();
        if (!($site instanceof Site)) {
            return;
        }
        $configuration = $this->siteConfigurationService->getConfiguration($site);

        $fallback = $configuration->getFallbackDetectionLanguage();
        foreach ($site->getLanguages() as $siteLanguage) {
            if ($siteLanguage->getLanguageId() === $fallback) {
                $event->setSelectedLanguage($siteLanguage);

                return;
            }
        }
    }
}
