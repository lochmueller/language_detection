<?php

namespace LD\LanguageDetection\Event;

use TYPO3\CMS\Core\Site\Entity\Site;

class HandleLanguageDetection
{

    /**
     * @var Site
     */
    protected $site;

    /**
     * @var bool
     */
    protected $handleLanguageDetection = true;

    /**
     * HandleLanguageDetection constructor.
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * @return Site
     */
    public function getSite(): Site
    {
        return $this->site;
    }

    /**
     * @return bool
     */
    public function isHandleLanguageDetection(): bool
    {
        return $this->handleLanguageDetection;
    }

    public function disableLanguageDetection(): void
    {
        $this->handleLanguageDetection = false;
    }
}
