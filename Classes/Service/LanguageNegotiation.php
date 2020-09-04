<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

class LanguageNegotiation
{
    protected Normalizer $normalizer;

    public function __construct(Normalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function findBestSiteLanguage(array $userLanguages, array $siteLanguages): ?SiteLanguage
    {
        $compareWith = [
            'getLocale',
            'getTwoLetterIsoCode',
        ];
        foreach ($compareWith as $function) {
            foreach ($userLanguages as $userLanguage) {
                foreach ($siteLanguages as $siteLanguage) {
                    /** @var $siteLanguage SiteLanguage */
                    if ($userLanguage === $this->normalizer->normalize((string)$siteLanguage->$function())) {
                        return $siteLanguage;
                    }
                }
            }
        }

        return null;
    }
}
