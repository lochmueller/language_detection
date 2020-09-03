<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Event;

use TYPO3\CMS\Core\Site\Entity\Site;

class HandleLanguageDetection
{
    protected Site $site;

    protected bool $handleLanguageDetection = true;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function isHandleLanguageDetection(): bool
    {
        return $this->handleLanguageDetection;
    }

    public function disableLanguageDetection(): void
    {
        $this->handleLanguageDetection = false;
    }
}
