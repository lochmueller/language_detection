<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BrowserLanguage
{
    /**
     * Returns the browser languages ordered by quality.
     *
     * @see https://tools.ietf.org/html/rfc7231#section-5.3.1
     */
    public function get(ServerRequestInterface $request): array
    {
        $languages = GeneralUtility::trimExplode(
            ',',
            implode(',', $request->getHeader('accept-language')),
            true
        );

        // Set default quality
        $acceptedLanguagesArr = [];
        foreach ($languages as $languageAndQualityStr) {
            list($languageCode, $quality) = GeneralUtility::trimExplode(';', $languageAndQualityStr, true);
            $acceptedLanguagesArr[$languageCode] = $quality ? (float)mb_substr($quality, 2) : 1.0;
        }

        // Sort
        arsort($acceptedLanguagesArr);

        // Remove quality 0.0
        $acceptedLanguagesArr = array_filter($acceptedLanguagesArr, function ($value, $key) {
            return 0.0 !== $value;
        }, ARRAY_FILTER_USE_BOTH);

        return array_keys($acceptedLanguagesArr);
    }
}
