<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Detect;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BrowserLanguageDetect
{
    public function __invoke(DetectUserLanguagesEvent $event): void
    {
        $languages = GeneralUtility::trimExplode(
            ',',
            implode(',', $event->getRequest()->getHeader('accept-language')),
            true
        );

        // Set default quality
        $acceptedLanguagesArr = [];
        foreach ($languages as $languageAndQualityStr) {
            if (str_contains($languageAndQualityStr, ';')) {
                $parts = GeneralUtility::trimExplode(';', $languageAndQualityStr, true);
                $languageCode = $parts[0];
                $quality = $parts[1] ?? '';
            } else {
                $languageCode = $languageAndQualityStr;
                $quality = 'q=1.0';
            }
            $acceptedLanguagesArr[$languageCode] = $quality !== '' ? (float)mb_substr($quality, 2) : 1.0;
        }

        // Sort
        arsort($acceptedLanguagesArr);

        // Remove quality 0.0
        $acceptedLanguagesArr = array_filter($acceptedLanguagesArr, fn($value, $key): bool => $value !== 0.0, \ARRAY_FILTER_USE_BOTH);

        $event->setUserLanguages(LocaleCollection::fromArray(array_keys($acceptedLanguagesArr)));
    }
}
