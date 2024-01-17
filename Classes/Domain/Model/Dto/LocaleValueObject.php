<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Domain\Model\Dto;

class LocaleValueObject
{
    public function __construct(protected string $locale) {}

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function __toString()
    {
        return $this->getLocale();
    }
}
