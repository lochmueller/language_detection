<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Listener;

use LD\LanguageDetection\Event\HandleLanguageDetection;

class WorkspacePreviewListener
{
    public function __invoke(HandleLanguageDetection $event): void
    {
        $arguments = $event->getRequest()->getQueryParams();
        $argumentsNames = array_keys($arguments);
        if (\in_array('ADMCMD_prev', $argumentsNames) || \in_array('ADMCMD_previewWS', $argumentsNames)) {
            $event->disableLanguageDetection();
        }
    }
}
