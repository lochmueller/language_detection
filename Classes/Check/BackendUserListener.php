<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Check;

use Lochmueller\LanguageDetection\Event\CheckLanguageDetection;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserListener
{
    protected SiteConfigurationService $siteConfigurationService;

    public function __construct(SiteConfigurationService $siteConfigurationService)
    {
        $this->siteConfigurationService = $siteConfigurationService;
    }

    public function __invoke(CheckLanguageDetection $event): void
    {
        if (!$this->siteConfigurationService->getConfiguration($event->getSite())->isDisableRedirectWithBackendSession()) {
            return;
        }
        if (GeneralUtility::makeInstance(Context::class)->getAspect('backend.user')->get('isLoggedIn')) {
            $event->disableLanguageDetection();
        }
    }
}
