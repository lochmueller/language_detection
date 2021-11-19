<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Event;

use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

final class NegotiateSiteLanguage extends AbstractEvent implements StoppableEventInterface
{
    private SiteInterface $site;
    private ServerRequestInterface $request;

    /**
     * @return array<string>
     */
    private array $userLanguages;
    private ?SiteLanguage $selectedLanguage = null;

    /**
     * @param array<string> $userLanguages
     */
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

    /**
     * @return array<string>
     */
    public function getUserLanguages(): array
    {
        return $this->userLanguages;
    }

    public function getSelectedLanguage(): ?SiteLanguage
    {
        return $this->selectedLanguage;
    }

    public function setSelectedLanguage(SiteLanguage $selectedLanguage): void
    {
        $this->selectedLanguage = $selectedLanguage;
    }

    public function isPropagationStopped(): bool
    {
        return null !== $this->selectedLanguage;
    }
}
