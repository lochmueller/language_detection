<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Event;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

final class NegotiateSiteLanguageEvent extends AbstractEvent implements StoppableEventInterface
{
    private SiteInterface $site;
    private ServerRequestInterface $request;
    private LocaleCollection $userLanguages;
    private ?SiteLanguage $selectedLanguage = null;

    public function __construct(SiteInterface $site, ServerRequestInterface $request, LocaleCollection $userLanguages)
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

    public function getUserLanguages(): LocaleCollection
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
