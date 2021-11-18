<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use LD\LanguageDetection\Service\SiteConfigurationService;

class PathListener
{
    protected SiteConfigurationService $siteConfigurationService;

    public function __construct(SiteConfigurationService $siteConfigurationService)
    {
        $this->siteConfigurationService = $siteConfigurationService;
    }

    public function __invoke(CheckLanguageDetection $event): void
    {
        if ($this->siteConfigurationService->getConfiguration($event->getSite())->isAllowAllPaths()) {
            return;
        }

        if ('/' !== $event->getRequest()->getUri()->getPath()) {
            $event->disableLanguageDetection();
        }
    }
}
