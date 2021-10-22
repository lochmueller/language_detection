<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Handler\Exception;

use Throwable;

class DisableLanguageDetectionException extends AbstractHandlerException
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct('Disable language detection', 1_236_781, $previous);
    }
}
