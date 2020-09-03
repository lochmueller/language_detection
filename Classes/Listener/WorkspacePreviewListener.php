<?php

namespace LD\LanguageDetection\Listener;

use LD\LanguageDetection\Event\HandleLanguageDetection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class WorkspacePreviewListener
{
    public function __invoke(HandleLanguageDetection $event)
    {
        if (null !== GeneralUtility::_GP('ADMCMD_prev') || null !== GeneralUtility::_GP('ADMCMD_previewWS')) {
            $event->disableLanguageDetection();
        }
    }
}
