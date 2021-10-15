<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Negotiation;

use LD\LanguageDetection\Event\NegotiateSiteLanguage;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\MathUtility;

class FallbackNegotiation
{
    protected const CONFIG_KEY = 'fallbackDetectionLanguage';

    public function __invoke(NegotiateSiteLanguage $event): void
    {
        $site = $event->getSite();
        $configuration = $site->getConfiguration();

        if (!isset($configuration[self::CONFIG_KEY]) || !MathUtility::canBeInterpretedAsInteger($configuration[self::CONFIG_KEY])) {
            return;
        }

        $fallback = (int)$configuration[self::CONFIG_KEY];

        foreach ($site->getAllLanguages() as $siteLanguage) {
            /** @var SiteLanguage $siteLanguage */
            if ($siteLanguage->getLanguageId() === $fallback) {
                $event->setSelectedLanguage($siteLanguage);

                return;
            }
        }
    }
}
