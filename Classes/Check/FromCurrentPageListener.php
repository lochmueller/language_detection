<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Check;

use Lochmueller\LanguageDetection\Event\CheckLanguageDetection;

class FromCurrentPageListener
{
    public function __invoke(CheckLanguageDetection $event): void
    {
        $serverInformation = $event->getRequest()->getServerParams();

        $referer = $serverInformation['HTTP_REFERER'] ?? '';
        $baseUri = rtrim((string)$event->getSite()->getBase(), '/');
        if ('' !== $referer && '' !== $baseUri && str_starts_with((string)$referer, $baseUri)) {
            $event->disableLanguageDetection();
        }
    }
}
