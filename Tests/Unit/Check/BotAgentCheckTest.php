<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Check;

use Lochmueller\LanguageDetection\Check\BotAgentCheck;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Check\BotAgentCheck::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent::class)]
class BotAgentCheckTest extends AbstractUnitTest
{
    #[DataProvider('data')]
    public function testUserAgents(string $userAgent, bool $result): void
    {
        $site = self::createStub(SiteInterface::class);

        $request = new ServerRequest(null, null, 'php://input', ['user-agent' => $userAgent]);
        $event = new CheckLanguageDetectionEvent($site, $request);

        $botAgentCheck = new BotAgentCheck();
        $botAgentCheck($event);

        self::assertEquals($result, $event->isLanguageDetectionEnable());
    }

    public function testWithoutUserAgent(): void
    {
        $site = self::createStub(SiteInterface::class);

        $request = new ServerRequest();
        $event = new CheckLanguageDetectionEvent($site, $request);

        $botAgentCheck = new BotAgentCheck();
        $botAgentCheck($event);

        self::assertTrue($event->isLanguageDetectionEnable());
    }

    /**
     * @return array<int, array<string|bool>>
     */
    public static function data(): array
    {
        return [
            ['AdsBot-Google', false],
            ['Firefox', true],
            ['Chrome', true],
            ['Yandex-12378', false],
        ];
    }
}
