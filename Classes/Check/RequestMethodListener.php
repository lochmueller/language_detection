<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;

class RequestMethodListener
{
    public function __invoke(CheckLanguageDetection $event): void
    {
        if ('GET' !== $event->getRequest()->getMethod()) {
            $event->disableLanguageDetection();
        }
    }
}
