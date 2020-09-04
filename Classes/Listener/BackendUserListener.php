<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Listener;

use Exception;
use LD\LanguageDetection\Event\HandleLanguageDetection;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserListener
{
    public function __invoke(HandleLanguageDetection $event): void
    {
        $config = $event->getSite()->getConfiguration();
        $disableForBackendUser = $config['disableRedirectWithBackendSession'] ?? false;
        try {
            if ($disableForBackendUser && GeneralUtility::makeInstance(Context::class)->getAspect('backend.user')->get('isLoggedIn')) {
                $event->disableLanguageDetection();
            }
        } catch (Exception $exception) {
        }
    }
}
