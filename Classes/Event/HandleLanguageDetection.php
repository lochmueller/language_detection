<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Event;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

class HandleLanguageDetection
{
    protected Site $site;
    protected ServerRequestInterface $request;

    protected bool $handleLanguageDetection = true;

    public function __construct(Site $site, ServerRequestInterface $request)
    {
        $this->site = $site;
        $this->request = $request;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
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
