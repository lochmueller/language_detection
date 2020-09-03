<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Listener;

use LD\LanguageDetection\Event\HandleLanguageDetection;

class PathListener
{
    public function __invoke(HandleLanguageDetection $event): void
    {
        if ('/' !== $event->getRequest()->getUri()->getPath()) {
            $event->disableLanguageDetection();
        }
    }
}
