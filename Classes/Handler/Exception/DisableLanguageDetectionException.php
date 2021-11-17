<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Handler\Exception;

class DisableLanguageDetectionException extends AbstractHandlerException
{
    /**
     * @var string
     */
    protected $message = 'Disable language detection';

    /**
     * @var int
     */
    protected $code = 1_236_781;
}
