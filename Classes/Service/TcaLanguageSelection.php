<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaLanguageSelection
{
    public function get(array &$configuration): void
    {
        if (!isset($configuration['row']['identifier'])) {
            return;
        }

        /** @var SiteFinder $siteFinder */
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);

        try {
            $site = $siteFinder->getSiteByIdentifier($configuration['row']['identifier']);
        } catch (\Exception $exception) {
            return;
        }

        $configuration['items'][] = ['', ''];
        foreach ($site->getAllLanguages() as $language) {
            /* @var $language SiteLanguage */
            $configuration['items'][] = [$language->getTitle(), $language->getLanguageId()];
        }
    }
}
