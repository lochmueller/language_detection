<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Listener;

use LD\LanguageDetection\Event\HandleLanguageDetection;
use TYPO3\CMS\Core\Utility\StringUtility;

class FromCurrentPageListener
{
    public function __invoke(HandleLanguageDetection $event): void
    {
        $serverInformation = $event->getRequest()->getServerParams();

        $referer = $serverInformation['HTTP_REFERER'] ?? '';
        $baseUri = rtrim((string)$event->getSite()->getBase(), '/');
        if ('' !== $referer && StringUtility::beginsWith((string)$referer, $baseUri)) {
            $event->disableLanguageDetection();
        }
    }
}
