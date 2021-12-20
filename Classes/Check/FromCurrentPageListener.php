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

    /**
     * @note Migrate to str_starts_with if PHP 7.4 support is dropped
     */
    protected function stringBeginsWith(string $haystack, string $needle): bool
    {
        return '' !== $needle && 0 === strpos($haystack, $needle);
    }
}
