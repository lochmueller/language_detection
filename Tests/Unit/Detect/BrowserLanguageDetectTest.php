<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Detect;

use Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect;
use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 * @coversNothing
 */
class BrowserLanguageDetectTest extends AbstractUnitTest
{
    public function testClassIsInvokable(): void
    {
        $class = new BrowserLanguageDetect();
        self::assertIsCallable($class);
    }

    /**
     * @dataProvider data
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent
     *
     * @param string[] $result
     */
    public function testFetchLanguageResults(string $acceptLanguage, array $result): void
    {
        $siteMock = $this->createMock(SiteInterface::class);
        $serverRequest = new ServerRequest(null, null, 'php://input', ['accept-language' => $acceptLanguage]);
        $event = new DetectUserLanguagesEvent($siteMock, $serverRequest);

        $class = new BrowserLanguageDetect();
        $class($event);

        self::assertEquals($result, $event->getUserLanguages()->toArray());
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
