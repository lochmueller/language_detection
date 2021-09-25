<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Event;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

final class NegotiateSiteLanguage extends AbstractEvent
{
    private SiteInterface $site;
    private ServerRequestInterface $request;
    private array $userLanguages;
    private ?SiteLanguage $selectedLanguage = null;

    public function __construct(SiteInterface $site, ServerRequestInterface $request, array $userLanguages)
    {
        $this->site = $site;
        $this->request = $request;
        $this->userLanguages = $userLanguages;
    }

    public function getSite(): SiteInterface
    {
        return $this->site;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function getUserLanguages(): array
    {
        return $this->userLanguages;
    }

    public function setUserLanguages(array $userLanguages): void
    {
        $this->userLanguages = $userLanguages;
    }

    public function getSelectedLanguage(): ?SiteLanguage
    {
        return $this->selectedLanguage;
    }

    public function setSelectedLanguage(SiteLanguage $selectedLanguage): void
    {
        $this->selectedLanguage = $selectedLanguage;
    }
}
