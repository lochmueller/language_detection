<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Check;

use Lochmueller\LanguageDetection\Check\FromCurrentPageListener;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetection;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractTest;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 * @coversNothing
 */
class FromCurrentPageListenerTest extends AbstractTest
{
    /**
     * @covers       \Lochmueller\LanguageDetection\Check\FromCurrentPageListener
     * @covers       \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @dataProvider data
     */
    public function testInvalidReferrer(string $referrer, string $baseUri, bool $isStillEnabled): void
    {
        $site = $this->createStub(SiteInterface::class);
        $site->method('getBase')->willReturn(new Uri($baseUri));

        $request = new ServerRequest(null, null, 'php://input', [], ['HTTP_REFERER' => $referrer]);
        $event = new CheckLanguageDetection($site, $request);

        $botListener = new FromCurrentPageListener();
        $botListener($event);

        self::assertEquals($isStillEnabled, $event->isLanguageDetectionEnable());
    }

    /**
     * @return array<string, array<int, bool|string>>
     */
    public function data(): array
    {
        return [
            'Internal referrer as deeplink' => ['https://www.website.de/deeplink', 'https://www.website.de', false],
            'Without referrer' => ['', 'https://www.website.de', true],
            'External referrer as deeplink' => ['https://www.google.de/other-side', 'https://www.website.de', true],
            'Referrer as homepage' => ['https://www.website.de', 'https://www.website.de', false],
        ];
    }
}
