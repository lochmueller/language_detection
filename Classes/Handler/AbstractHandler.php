<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Handler;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

abstract class AbstractHandler
{
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function getSiteFromRequest(ServerRequestInterface $request): SiteInterface
    {
        $site = $request->getAttribute('site');
        if (!($site instanceof SiteInterface)) {
            throw new \Exception('Found no valid site object in request', 1_637_813_123);
        }

        return $site;
    }
}
