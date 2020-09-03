<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Listener;

use LD\LanguageDetection\Event\HandleLanguageDetection;

class EnableListener
{
    public function __invoke(HandleLanguageDetection $event): void
    {
        $config = $event->getSite()->getConfiguration();

        $enable = $config['enableLanguageDetection'] ?? true;
        if (!$enable) {
            $event->disableLanguageDetection();
        }
    }
}
