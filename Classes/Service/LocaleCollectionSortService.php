<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Service;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject;

class LocaleCollectionSortService
{
    public const SORT_DEFAULT = self::SORT_AFTER;
    public const SORT_AFTER = 'after';
    public const SORT_BEFORE = 'before';
    public const SORT_REPLACE = 'replace';

    public function addLocaleByMode(LocaleCollection $collection, LocaleValueObject $locale, string $mode = self::SORT_DEFAULT): LocaleCollection
    {
        $base = $collection->toArray();
        switch ($mode) {
            case self::SORT_BEFORE:
                array_unshift($base, $locale);
                break;
            case self::SORT_REPLACE:
                $base = [$locale];
                break;
            case self::SORT_AFTER:
            default:
                $base[] = $locale;
                break;
        }

        return LocaleCollection::fromArrayLocales($base);
    }
}
