<?php

namespace LD\LanguageDetection\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class BrowserLanguage
{
    /**
     * Returns the browser languages ordered by quality.
     *
     * @return array
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.1
     */
    public function get(): array
    {
        $languages = GeneralUtility::trimExplode(
            ',',
            GeneralUtility::getIndpEnv('HTTP_ACCEPT_LANGUAGE'),
            true
        );

        // Set default quality
        $acceptedLanguagesArr = [];
        foreach ($languages as $languageAndQualityStr) {
            list($languageCode, $quality) = GeneralUtility::trimExplode(';', $languageAndQualityStr, true);
            $acceptedLanguagesArr[$languageCode] = $quality ? (float)\mb_substr($quality, 2) : 1.0;
        }

        // Sort
        arsort($acceptedLanguagesArr);

        // Remove quality 0.0
        $acceptedLanguagesArr = array_filter($acceptedLanguagesArr, function ($value, $key) {
            return $value !== 0.0;
        }, ARRAY_FILTER_USE_BOTH);

        return array_keys($acceptedLanguagesArr);
    }
}
