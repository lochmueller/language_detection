<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Check;

use Lochmueller\LanguageDetection\Event\CheckLanguageDetection;

class RequestMethodCheck
{
    public function __invoke(CheckLanguageDetection $event): void
    {
        if ('GET' !== $event->getRequest()->getMethod()) {
            $event->disableLanguageDetection();
        }
    }
}
