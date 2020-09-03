<?php

namespace LD\LanguageDetection\Listener;

use LD\LanguageDetection\Event\HandleLanguageDetection;

class EnableListener
{
    public function __invoke(HandleLanguageDetection $event)
    {
        $config = $event->getSite()->getConfiguration();

        $enable = $config['enableLanguageDetection'] ?? true;
        if (!$enable) {
            $event->disableLanguageDetection();
        }
    }
}
