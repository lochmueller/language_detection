<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Service;

use Locale;
use ResourceBundle;

class LanguageService
{
    public function getLanguageByCountry(string $country): string
    {
        $subtags = ResourceBundle::create('likelySubtags', 'ICUDATA', false);
        if (!$subtags instanceof ResourceBundle) {
            return 'xx';
        }
        $dummy = 'und_' . strtoupper($country);
        $locale = $subtags->get($dummy) ?: $subtags->get('und');
        if (!\is_string($locale)) {
            return 'xx';
        }

        return Locale::getPrimaryLanguage($locale) ?? 'xx';
    }
}
