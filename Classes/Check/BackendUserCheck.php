<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Check;

use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserCheck
{
    protected SiteConfigurationService $siteConfigurationService;

    public function __construct(SiteConfigurationService $siteConfigurationService)
    {
        $this->siteConfigurationService = $siteConfigurationService;
    }

    public function __invoke(CheckLanguageDetectionEvent $event): void
    {
        if (!$this->siteConfigurationService->getConfiguration($event->getSite())->isDisableRedirectWithBackendSession()) {
            return;
        }
        /** @var Context $context */
        $context = GeneralUtility::makeInstance(Context::class);
        $isLoggedIn = (bool)$context->getAspect('backend.user')->get('isLoggedIn');
        if ($isLoggedIn) {
            $event->disableLanguageDetection();
        }
    }
}
