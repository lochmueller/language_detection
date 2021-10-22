<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use Locale;
use ResourceBundle;

class LanguageService
{
    public function getLanguageByCountry(string $country): string
    {
        $subtags = ResourceBundle::create('likelySubtags', 'ICUDATA', false);
        $dummy = 'und_' . strtoupper($country);
        $locale = $subtags->get($dummy) ?: $subtags->get('und');

        return Locale::getPrimaryLanguage($locale);
    }
}
