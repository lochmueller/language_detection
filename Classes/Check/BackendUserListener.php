<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserListener
{
    public function __invoke(CheckLanguageDetection $event): void
    {
        $config = $event->getSite()->getConfiguration();
        $disableForBackendUser = $config['disableRedirectWithBackendSession'] ?? false;
        if (!$disableForBackendUser) {
            return;
        }
        if (GeneralUtility::makeInstance(Context::class)->getAspect('backend.user')->get('isLoggedIn')) {
            $event->disableLanguageDetection();
        }
    }
}
