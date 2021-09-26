<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Detect;

use LD\LanguageDetection\Event\DetectUserLanguages;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BrowserLanguage
{
    public function __invoke(DetectUserLanguages $event): void
    {
        $languages = GeneralUtility::trimExplode(
            ',',
            implode(',', $event->getRequest()->getHeader('accept-language')),
            true
        );

        // Set default quality
        $acceptedLanguagesArr = [];
        foreach ($languages as $languageAndQualityStr) {
            [$languageCode, $quality] = GeneralUtility::trimExplode(';', $languageAndQualityStr, true);
            $acceptedLanguagesArr[$languageCode] = $quality ? (float)mb_substr($quality, 2) : 1.0;
        }

        // Sort
        arsort($acceptedLanguagesArr);

        // Remove quality 0.0
        $acceptedLanguagesArr = array_filter($acceptedLanguagesArr, fn ($value, $key): bool => 0.0 !== $value, \ARRAY_FILTER_USE_BOTH);

        $event->setUserLanguages(array_keys($acceptedLanguagesArr));
    }
}
