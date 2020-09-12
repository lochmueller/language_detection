<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use TYPO3\CMS\Core\Utility\StringUtility;

class FromCurrentPageListener
{
    public function __invoke(CheckLanguageDetection $event): void
    {
        $serverInformation = $event->getRequest()->getServerParams();

        $referer = $serverInformation['HTTP_REFERER'] ?? '';
        $baseUri = rtrim((string)$event->getSite()->getBase(), '/');
        if ('' !== $referer && StringUtility::beginsWith((string)$referer, $baseUri)) {
            $event->disableLanguageDetection();
        }
    }
}
