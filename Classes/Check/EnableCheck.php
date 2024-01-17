<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Check;

use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;

class EnableCheck
{
    public function __construct(protected SiteConfigurationService $siteConfigurationService) {}

    public function __invoke(CheckLanguageDetectionEvent $event): void
    {
        if (!$this->siteConfigurationService->getConfiguration($event->getSite())->isEnableLanguageDetection()) {
            $event->disableLanguageDetection();
        }
    }
}
