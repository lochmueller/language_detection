<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use LD\LanguageDetection\Service\SiteConfigurationService;

class EnableListener
{
    protected SiteConfigurationService $siteConfigurationService;

    public function __construct(SiteConfigurationService $siteConfigurationService)
    {
        $this->siteConfigurationService = $siteConfigurationService;
    }

    public function __invoke(CheckLanguageDetection $event): void
    {
        if (!$this->siteConfigurationService->getConfiguration($event->getSite())->isEnableLanguageDetection()) {
            $event->disableLanguageDetection();
        }
    }
}
