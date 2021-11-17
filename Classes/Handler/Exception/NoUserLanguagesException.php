<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Handler\Exception;

class NoUserLanguagesException extends AbstractHandlerException
{
    /**
     * @var string
     */
    protected $message = 'No user languages';

    /**
     * @var int
     */
    protected $code = 3_284_924;
}
