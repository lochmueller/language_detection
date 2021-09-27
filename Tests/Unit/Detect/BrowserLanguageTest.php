<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Service;

use LD\LanguageDetection\Detect\BrowserLanguage;
use LD\LanguageDetection\Event\DetectUserLanguages;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 * @coversNothing
 */
class BrowserLanguageTest extends AbstractTest
{
    public function testClassIsInvokable(): void
    {
        $class = new BrowserLanguage();
        self::assertIsCallable($class);
    }

    /**
     * @dataProvider data
     * @covers \LD\LanguageDetection\Detect\BrowserLanguage
     * @covers \LD\LanguageDetection\Event\DetectUserLanguages
     *
     * @param mixed[]|string[] $result
     */
    public function testFetchLanguageResults(string $acceptLanguage, array $result): void
    {
        $siteMock = $this->createMock(SiteInterface::class);
        $serverRequest = new ServerRequest(null, null, 'php://input', ['accept-language' => $acceptLanguage]);
        $event = new DetectUserLanguages($siteMock, $serverRequest);

        $class = new BrowserLanguage();
        $class($event);

        self::assertEquals($result, $event->getUserLanguages());
    }

    /**
     * @return array<int, mixed[]>
     */
    public function data(): array
    {
        return [
            ['', []],
            ['de', ['de']],
            ['de-CH', ['de-CH']],
            ['fr-CH, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5', ['fr-CH', 'fr', 'en', 'de', '*']],
            ['en-US,en;q=0.5', ['en-US', 'en']],
            ['en-US;q=0.5,en', ['en', 'en-US']],
        ];
    }
}
