<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Check;

use Lochmueller\LanguageDetection\Event\CheckLanguageDetection;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;

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
