<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Event;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

final class DetectUserLanguages
{
    protected Site $site;
    protected ServerRequestInterface $request;
    protected array $userLanguages = [];

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

    public function getUserLanguages(): array
    {
        return $this->userLanguages;
    }

    public function setUserLanguages(array $userLanguages): void
    {
        $this->userLanguages = $userLanguages;
    }
}
