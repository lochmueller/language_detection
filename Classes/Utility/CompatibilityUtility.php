<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Utility;

use TYPO3\CMS\Core\Utility\StringUtility;

class CompatibilityUtility
{
    public static function stringBeginsWith(string $haystack, string $needle): bool
    {
        if (\function_exists('str_starts_with')) {
            return str_starts_with($haystack, $needle);
        }

        return StringUtility::beginsWith($haystack, $needle);
    }
}
