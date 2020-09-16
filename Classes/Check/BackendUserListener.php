<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use Exception;
use LD\LanguageDetection\Event\CheckLanguageDetection;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserListener
{
    public function __invoke(CheckLanguageDetection $event): void
    {
        if (!($event->getSite() instanceof Site)) {
            // Wrong site type e.g. NullSite
            $event->disableLanguageDetection();

            return;
        }
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
