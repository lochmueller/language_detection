<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;

class PathListener
{
    public function __invoke(CheckLanguageDetection $event): void
    {
        if ('/' !== $event->getRequest()->getUri()->getPath()) {
            $event->disableLanguageDetection();
        }
    }
}
