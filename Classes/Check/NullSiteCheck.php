<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Check;

use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Service\GlobalConfigurationService;
use TYPO3\CMS\Core\Site\Entity\NullSite;

/**
 * The request does not match any site (e.g. there is no site with the root base "/").
 * Handle this case only, if the feature is explicitly enabled in the extension configuration.
 */
class NullSiteCheck
{
    public function __construct(protected GlobalConfigurationService $globalConfigurationService) {}

    public function __invoke(CheckLanguageDetectionEvent $event): void
    {
        if (!($event->getSite() instanceof NullSite)) {
            return;
        }

        if (!$this->globalConfigurationService->getConfiguration()->isEnableNullSiteDetection()) {
            $event->disableLanguageDetection();
        }
    }
}
