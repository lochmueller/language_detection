<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Handler\Exception;

use Throwable;

class NoResponseException extends AbstractHandlerException
{
    protected $message = 'No response was created';

    protected $code = 7_829_342;
}
