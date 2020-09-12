<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;

class WorkspacePreviewListener
{
    public function __invoke(CheckLanguageDetection $event): void
    {
        $arguments = $event->getRequest()->getQueryParams();
        $argumentsNames = array_keys($arguments);
        if (\in_array('ADMCMD_prev', $argumentsNames) || \in_array('ADMCMD_previewWS', $argumentsNames)) {
            $event->disableLanguageDetection();
        }
    }
}
