<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use LD\LanguageDetection\Utility\CompatibilityUtility;

class FromCurrentPageListener
{
    public function __invoke(CheckLanguageDetection $event): void
    {
        $serverInformation = $event->getRequest()->getServerParams();

        $referer = $serverInformation['HTTP_REFERER'] ?? '';
        $baseUri = rtrim((string)$event->getSite()->getBase(), '/');
        if ('' !== $referer && '' !== $baseUri && CompatibilityUtility::stringBeginsWith((string)$referer, $baseUri)) {
            $event->disableLanguageDetection();
        }
    }
}
