<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Handler\Exception;

use Throwable;

class NoSelectedLanguageException extends AbstractHandlerException
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct('No selectable language', 2_374_892, $previous);
    }
}
