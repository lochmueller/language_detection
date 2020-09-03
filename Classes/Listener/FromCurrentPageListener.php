<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Listener;

use LD\LanguageDetection\Event\HandleLanguageDetection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

class FromCurrentPageListener
{
    public function __invoke(HandleLanguageDetection $event): void
    {
        $referer = (string)GeneralUtility::getIndpEnv('HTTP_REFERER');
        $baseUri = rtrim((string)$event->getSite()->getBase(), '/');

        if ('' !== $referer && StringUtility::beginsWith($referer, $baseUri)) {
            $event->disableLanguageDetection();
        }
    }
}
