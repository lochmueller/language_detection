<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use TYPO3\CMS\Core\Site\Entity\Site;

class EnableListener
{
    public function __invoke(CheckLanguageDetection $event): void
    {
        if (!($event->getSite() instanceof Site)) {
            // Wrong site type e.g. NullSite
            $event->disableLanguageDetection();

            return;
        }
        $config = $event->getSite()->getConfiguration();

        $enable = $config['enableLanguageDetection'] ?? true;
        if (!$enable) {
            $event->disableLanguageDetection();
        }
    }
}
