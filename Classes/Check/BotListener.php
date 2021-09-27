<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use Psr\Http\Message\ServerRequestInterface;

class BotListener
{
    protected const BOT_PATTERN = '/bot|google|baidu|bing|msn|teoma|slurp|yandex/i';

    public function __invoke(CheckLanguageDetection $event): void
    {
        if ($this->isBot($event->getRequest())) {
            $event->disableLanguageDetection();
        }
    }

    protected function isBot(ServerRequestInterface $request): bool
    {
        return preg_match(self::BOT_PATTERN, $request->getHeaderLine('user-agent'));
    }
}
