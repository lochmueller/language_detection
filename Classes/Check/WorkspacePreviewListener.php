<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;

class WorkspacePreviewListener
{
    public function __invoke(CheckLanguageDetection $event): void
    {
        $arguments = $event->getRequest()->getQueryParams();
        if (\array_key_exists('ADMCMD_prev', $arguments) || \array_key_exists('ADMCMD_previewWS', $arguments)) {
            $event->disableLanguageDetection();
        }
    }
}
