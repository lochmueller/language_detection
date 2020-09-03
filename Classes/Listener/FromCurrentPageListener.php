<?php

namespace LD\LanguageDetection\Listener;

use LD\LanguageDetection\Event\HandleLanguageDetection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class FromCurrentPageListener
{
    public function __invoke(HandleLanguageDetection $event)
    {
        $referer = (string)GeneralUtility::getIndpEnv('HTTP_REFERER');

        DebuggerUtility::var_dump('Call');
        die();
        // @todo Handle

        $baseUrl = $GLOBALS['TSFE']->baseUrl;
        if (\mb_strlen($referer) && (false !== \mb_stripos(
            $referer,
            GeneralUtility::getIndpEnv('TYPO3_SITE_URL')
        ) || false !== \mb_stripos(
            $referer,
            $baseUrl
        ) || false !== \mb_stripos(
                    $referer . '/',
                    GeneralUtility::getIndpEnv('TYPO3_SITE_URL')
                ) || false !== \mb_stripos(
                    $referer . '/',
                    $baseUrl
                ))
        ) {
            $event->disableLanguageDetection();
        }
    }
}
