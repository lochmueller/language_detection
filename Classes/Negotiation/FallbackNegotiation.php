<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Negotiation;

use LD\LanguageDetection\Event\NegotiateSiteLanguage;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\MathUtility;

class FallbackNegotiation
{
    public function __invoke(NegotiateSiteLanguage $event): void
    {
        $site = $event->getSite();
        if (!$site instanceof Site) {
            return;
        }

        $configuration = $event->getSite()->getConfiguration();

        if (!isset($configuration['fallbackDetectionLanguage']) || !MathUtility::canBeInterpretedAsInteger($configuration['fallbackDetectionLanguage'])) {
            return;
        }

        $fallback = (int)$configuration['fallbackDetectionLanguage'];

        foreach ($site->getAllLanguages() as $siteLanguage) {
            /** @var SiteLanguage $siteLanguage */
            if ($siteLanguage->getLanguageId() === $fallback) {
                $event->setSelectedLanguage($siteLanguage);

                return;
            }
        }
    }
}
