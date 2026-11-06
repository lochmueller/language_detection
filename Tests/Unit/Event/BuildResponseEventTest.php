<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Event;

use Lochmueller\LanguageDetection\Event\BuildResponseEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Event\BuildResponseEvent::class)]
class BuildResponseEventTest extends AbstractEventTest
{
    public function testEventGetterAndSetter(): void
    {
        $site = self::createStub(SiteInterface::class);
        $request = self::createStub(ServerRequestInterface::class);
        $language = self::createStub(SiteLanguage::class);

        $event = new BuildResponseEvent($site, $request, $language);

        self::assertNull($event->getResponse());
        self::assertEquals($site, $event->getSite());
        self::assertEquals($request, $event->getRequest());
        self::assertEquals($language, $event->getSelectedLanguage());
        self::assertFalse($event->isPropagationStopped());

        $response = self::createStub(ResponseInterface::class);

        $event->setResponse($response);
        self::assertEquals($response, $event->getResponse());
        self::assertTrue($event->isPropagationStopped());
    }
}
