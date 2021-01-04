<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use TYPO3\CMS\Core\Site\Entity\Site;

class PathListener
{
    public function __invoke(CheckLanguageDetection $event): void
    {
        if (!($event->getSite() instanceof Site)) {

            return;
        }
        $config = $event->getSite()->getConfiguration();
        if ($config['allowAllPaths'] ?? false) {
            return;
        }

        if ('/' !== $event->getRequest()->getUri()->getPath()) {
            $event->disableLanguageDetection();
        }
    }
}
