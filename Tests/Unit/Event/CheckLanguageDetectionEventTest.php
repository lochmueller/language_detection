<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Event;

use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent::class)]
class CheckLanguageDetectionEventTest extends AbstractEventTest
{
    public function testEventGetterAndSetter(): void
    {
        $site = self::createStub(SiteInterface::class);
        $request = self::createStub(ServerRequestInterface::class);

        $event = new CheckLanguageDetectionEvent($site, $request);

        self::assertEquals($site, $event->getSite());
        self::assertEquals($request, $event->getRequest());
        self::assertTrue($event->isLanguageDetectionEnable());

        $event->disableLanguageDetection();

        self::assertFalse($event->isLanguageDetectionEnable());
    }
}
