<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Detect;

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
     * @param string[] $result
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
     * @return array<string, array<int, array<int, string>|string>>
     */
    public function data(): array
    {
        return [
            'empty' => ['', []],
            'default' => ['de', ['de']],
            'default locale' => ['de-CH', ['de-CH']],
            'multiple locale incl. quality and spaces' => ['fr-CH, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5', ['fr-CH', 'fr', 'en', 'de', '*']],
            'multiple locale incl. quality' => ['en-US,en;q=0.5', ['en-US', 'en']],
            'multiple locale incl. quality in wrong order' => ['en-US;q=0.5,en', ['en', 'en-US']],
            'multiple locale incl. quality and q = 0' => ['en-US;q=0.0,en', ['en']],
        ];
    }
}
