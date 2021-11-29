<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Event;

use Lochmueller\LanguageDetection\Event\BuildResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @internal
 * @coversNothing
 */
class BuildResponseTest extends AbstractEventTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponse
     */
    public function testEventGetterAndSetter(): void
    {
        $site = $this->createMock(SiteInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $language = $this->createMock(SiteLanguage::class);

        $event = new BuildResponse($site, $request, $language);

        self::assertNull($event->getResponse());
        self::assertEquals($site, $event->getSite());
        self::assertEquals($request, $event->getRequest());
        self::assertEquals($language, $event->getSelectedLanguage());
        self::assertFalse($event->isPropagationStopped());

        $response = $this->createMock(ResponseInterface::class);

        $event->setResponse($response);
        self::assertEquals($response, $event->getResponse());
        self::assertTrue($event->isPropagationStopped());
    }
}
