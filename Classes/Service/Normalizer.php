<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Service;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject;

class Normalizer
{
    public function normalizeList(LocaleCollection $locales): LocaleCollection
    {
        $result = array_map(fn(LocaleValueObject $locale): string => $this->normalize((string)$locale), $locales->toArray());

        return LocaleCollection::fromArray($result);
    }

    public function normalize(string $locale): string
    {
        // Drop charset
        $pos = strpos($locale, '.');
        if ($pos !== false) {
            $locale = substr($locale, 0, $pos);
        }

        $code = str_replace('-', '_', $locale);
        $parts = explode('_', $code);

        $locale = strtolower($parts[0]);
        if (isset($parts[1])) {
            $locale .= '_' . strtoupper($parts[1]);
        }

        return $locale;
    }

    /**
     * Reduce a locale to the pure language code, so "de_CH" and "de-DE" both result in "de".
     */
    public function normalizeToLanguageCode(string $locale): string
    {
        return explode('_', $this->normalize($locale))[0];
    }
}
