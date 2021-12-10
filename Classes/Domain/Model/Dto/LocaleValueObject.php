<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Domain\Model\Dto;

class LocaleValueObject
{
    protected string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

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
