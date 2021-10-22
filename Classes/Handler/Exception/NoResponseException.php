<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Handler\Exception;

use Throwable;

class NoResponseException extends AbstractHandlerException
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct('No response was created', 7_829_342, $previous);
    }
}
