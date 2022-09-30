<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Service;

use Locale;
use ResourceBundle;

class LanguageService
{
    public function getLanguageByCountry(string $country): string
    {
        /** @var ResourceBundle $subtags */
        $subtags = ResourceBundle::create('likelySubtags', 'ICUDATA', false);
        $dummy = 'und_' . strtoupper($country);
        $locale = $subtags->get($dummy) ?: $subtags->get('und');

        return Locale::getPrimaryLanguage($locale);
    }
}
