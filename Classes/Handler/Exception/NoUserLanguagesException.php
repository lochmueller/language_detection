<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Handler\Exception;

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
