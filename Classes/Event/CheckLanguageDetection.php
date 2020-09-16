<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Event;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

final class CheckLanguageDetection
{
    protected SiteInterface $site;
    protected ServerRequestInterface $request;
    protected bool $handle = true;

    public function __construct(SiteInterface $site, ServerRequestInterface $request)
    {
        $this->site = $site;
        $this->request = $request;
    }

    public function getSite(): SiteInterface
    {
        return $this->site;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function isLanguageDetectionEnable(): bool
    {
        return $this->handle;
    }

    public function disableLanguageDetection(): void
    {
        $this->handle = false;
    }
}
