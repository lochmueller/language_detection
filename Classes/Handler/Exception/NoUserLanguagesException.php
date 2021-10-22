<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Handler\Exception;

use Throwable;

class NoUserLanguagesException extends AbstractHandlerException
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct('No user languages', 3_284_924, $previous);
    }
}
