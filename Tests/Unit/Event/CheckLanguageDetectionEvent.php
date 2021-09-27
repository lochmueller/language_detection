<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Event;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 * @coversNothing
 */
class CheckLanguageDetectionEvent extends AbstractEventTest
{
    /**
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     */
    public function testEventGetterAndSetter(): void
    {
        $site = $this->createMock(SiteInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);

        $event = new CheckLanguageDetection($site, $request);

        self::assertEquals($site, $event->getSite());
        self::assertEquals($request, $event->getRequest());
        self::assertTrue($event->isLanguageDetectionEnable());

        $event->disableLanguageDetection();

        self::assertFalse($event->isLanguageDetectionEnable());
    }
}
