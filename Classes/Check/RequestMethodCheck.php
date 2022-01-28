<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Check;

use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;

class RequestMethodCheck
{
    public function __invoke(CheckLanguageDetectionEvent $event): void
    {
        if ('GET' !== $event->getRequest()->getMethod()) {
            $event->disableLanguageDetection();
        }
    }
}
