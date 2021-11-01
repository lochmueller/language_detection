<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Handler\Exception;

use Throwable;

class NoSelectedLanguageException extends AbstractHandlerException
{
    protected $message = 'No selectable language';

    protected $code = 2_374_892;
}
