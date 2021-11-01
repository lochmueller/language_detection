<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Event;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

final class DetectUserLanguages extends AbstractEvent
{
    private SiteInterface $site;
    private ServerRequestInterface $request;

    /** @var array<string> */
    private array $userLanguages = [];

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

    /**
     * @return array<string>
     */
    public function getUserLanguages(): array
    {
        return $this->userLanguages;
    }

    /**
     * @param array<string> $userLanguages
     */
    public function setUserLanguages(array $userLanguages): void
    {
        $this->userLanguages = $userLanguages;
    }
}
