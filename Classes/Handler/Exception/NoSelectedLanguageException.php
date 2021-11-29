<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Handler\Exception;

class NoSelectedLanguageException extends AbstractHandlerException
{
    /**
     * @var string
     */
    protected $message = 'No selectable language';

    /**
     * @var int
     */
    protected $code = 2_374_892;
}
