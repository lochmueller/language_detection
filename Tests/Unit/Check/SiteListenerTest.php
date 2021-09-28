<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 * @coversNothing
 */
class SiteListenerTest extends AbstractTest
{
    /**
     * @covers \LD\LanguageDetection\Check\SiteListener
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     * @dataProvider data
     */
    public function testInvalidSiteObject(string $siteClass, bool $result): void
    {
        $site = $this->createMock($siteClass);
        $request = $this->createMock(ServerRequestInterface::class);
        $event = new CheckLanguageDetection($site, $request);

        $backendUserListener = new SiteListener();
        $backendUserListener($event);

        self::assertEquals($result, $event->isLanguageDetectionEnable());
    }

    /**
     * @return array<string, array<bool|class-string<\TYPO3\CMS\Core\Site\Entity\SiteInterface>>>
     */
    public function data(): array
    {
        return [
            'SiteInterface only' => [SiteInterface::class, false],
            'Real Site' => [Site::class, true],
            'Null Site' => [NullSite::class, false],
        ];
    }
}
