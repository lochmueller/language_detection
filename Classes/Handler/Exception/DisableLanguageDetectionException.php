<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Handler\Exception;

use Throwable;

class DisableLanguageDetectionException extends AbstractHandlerException
{
    protected $message = 'Disable language detection';

    protected $code = 1_236_781;
}
