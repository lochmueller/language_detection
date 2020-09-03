<?php

namespace LD\LanguageDetection\Service;

class Normalizer
{
    public function normalizeList(array $locales): array
    {
        return array_map([$this, 'normalize'], $locales);
    }

    public function normalize(string $locale): string
    {
        // Drop charset
        list($code, $charset) = explode('.', $locale);

        $code = str_replace('-', '_', $code);
        list($language, $region) = explode('_', $code);

        $locale = strtolower($language);
        if (isset($region)) {
            $locale .= '_' . strtoupper($region);
        }
        return $locale;
    }
}
