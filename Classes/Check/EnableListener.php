<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;

class EnableListener
{
    public function __invoke(CheckLanguageDetection $event): void
    {
        $config = $event->getSite()->getConfiguration();

        $enable = $config['enableLanguageDetection'] ?? true;
        if (!$enable) {
            $event->disableLanguageDetection();
        }
    }
}
