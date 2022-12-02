<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Domain\Collection;

use Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject;

class LocaleCollection
{
    /**
     * @var array<LocaleValueObject>
     */
    protected array $locales = [];

    public function add(LocaleValueObject $locale): void
    {
        $this->locales[] = $locale;
    }

    /**
     * @return array<LocaleValueObject>
     */
    public function toArray(): array
    {
        return $this->locales;
    }

    public function isEmpty(): bool
    {
        return $this->locales === [];
    }

    /**
     * @param array<string> $locales
     */
    public static function fromArray(array $locales): self
    {
        $collection = new self();
        foreach ($locales as $item) {
            $collection->add(new LocaleValueObject($item));
        }

        return $collection;
    }

    /**
     * @param array<LocaleValueObject> $locales
     */
    public static function fromArrayLocales(array $locales): self
    {
        $collection = new self();
        foreach ($locales as $item) {
            $collection->add($item);
        }

        return $collection;
    }
}
