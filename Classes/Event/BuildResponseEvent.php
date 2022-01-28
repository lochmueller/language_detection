<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Event;

use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

final class BuildResponseEvent extends AbstractEvent implements StoppableEventInterface
{
    private SiteInterface $site;
    private ServerRequestInterface $request;
    private SiteLanguage $selectedLanguage;
    private ?ResponseInterface $response = null;

    public function __construct(SiteInterface $site, ServerRequestInterface $request, SiteLanguage $selectedLanguage)
    {
        $this->site = $site;
        $this->request = $request;
        $this->selectedLanguage = $selectedLanguage;
    }

    public function getSite(): SiteInterface
    {
        return $this->site;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function getSelectedLanguage(): SiteLanguage
    {
        return $this->selectedLanguage;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    public function isPropagationStopped(): bool
    {
        return null !== $this->getResponse();
    }
}
