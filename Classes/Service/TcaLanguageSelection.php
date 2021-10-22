<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TcaLanguageSelection
{
    protected SiteFinder $siteFinder;

    public function __construct(?SiteFinder $siteFinder = null)
    {
        $this->siteFinder = $siteFinder ?? GeneralUtility::makeInstance(SiteFinder::class);
    }

    public function get(array &$configuration): void
    {
        if (!isset($configuration['row']['identifier'])) {
            return;
        }

        try {
            $site = $this->siteFinder->getSiteByIdentifier($configuration['row']['identifier']);
        } catch (SiteNotFoundException $exception) {
            return;
        }

        $configuration['items'][] = ['', ''];
        foreach ($site->getAllLanguages() as $language) {
            /* @var $language SiteLanguage */
            $configuration['items'][] = [$language->getTitle(), $language->getLanguageId()];
        }
    }
}
