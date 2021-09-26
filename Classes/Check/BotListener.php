<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

class BotListener
{
    protected const BOT_PATTERN = '/bot|google|baidu|bing|msn|teoma|slurp|yandex/i';

    public function __invoke(CheckLanguageDetection $event): void
    {
        if ($event->getSite() instanceof Site && $this->isBot($event->getRequest())) {
            $event->disableLanguageDetection();
        }
    }

    protected function isBot(ServerRequestInterface $request): bool
    {
        $userAgent = $request->getHeader('user-agent');

        if (\is_array($userAgent) && !empty($userAgent)) {
            $userAgent = array_shift($userAgent);
        }

        return \is_string($userAgent) && preg_match(self::BOT_PATTERN, $userAgent);
    }
}
