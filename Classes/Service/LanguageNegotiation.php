<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

class LanguageNegotiation
{
    /**
     * @todo Compoare $userLanguages & $siteLanguages
     */
    public function findBestSiteLanguage(array $userLanguages, array $siteLanguages): ?SiteLanguage
    {
    }
}
