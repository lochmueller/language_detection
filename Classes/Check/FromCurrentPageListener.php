<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Check;

use Lochmueller\LanguageDetection\Event\CheckLanguageDetection;

class FromCurrentPageListener
{
    public function __invoke(CheckLanguageDetection $event): void
    {
        $serverInformation = $event->getRequest()->getServerParams();

        $referer = $serverInformation['HTTP_REFERER'] ?? '';
        $baseUri = rtrim((string)$event->getSite()->getBase(), '/');
        if ('' !== $referer && '' !== $baseUri && $this->stringBeginsWith((string)$referer, $baseUri)) {
            $event->disableLanguageDetection();
        }
    }

    protected function stringBeginsWith(string $haystack, string $needle): bool
    {
        if (function_exists('str_starts_with')) {
            return str_starts_with($haystack, $needle);
        }

        return '' !== $needle && 0 === strpos($haystack, $needle);
    }
}
