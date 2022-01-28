<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Check;

use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;

class WorkspacePreviewCheck
{
    public function __invoke(CheckLanguageDetectionEvent $event): void
    {
        $arguments = $event->getRequest()->getQueryParams();
        if (\array_key_exists('ADMCMD_prev', $arguments) || \array_key_exists('ADMCMD_previewWS', $arguments)) {
            $event->disableLanguageDetection();
        }
    }
}
