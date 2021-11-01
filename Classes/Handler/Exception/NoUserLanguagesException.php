<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Handler\Exception;

use Throwable;

class NoUserLanguagesException extends AbstractHandlerException
{
    protected $message = 'No user languages';

    protected $code = 3_284_924;
}
