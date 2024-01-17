<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Event;

use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

final class CheckLanguageDetectionEvent extends AbstractEvent implements StoppableEventInterface
{
    private bool $handle = true;

    public function __construct(
        private SiteInterface $site,
        private ServerRequestInterface $request
    ) {}

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

    public function isPropagationStopped(): bool
    {
        return !$this->isLanguageDetectionEnable();
    }
}
