<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Handler\Exception;

class NoResponseException extends AbstractHandlerException
{
    /**
     * @var string
     */
    protected $message = 'No response was created';

    /**
     * @var int
     */
    protected $code = 7_829_342;
}
