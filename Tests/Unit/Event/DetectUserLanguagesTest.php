<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Event;

use LD\LanguageDetection\Event\DetectUserLanguages;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 * @coversNothing
 */
class DetectUserLanguagesTest extends AbstractEventTest
{
    /**
     * @covers \LD\LanguageDetection\Event\DetectUserLanguages
     */
    public function testEventGetterAndSetter(): void
    {
        $site = $this->createMock(SiteInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);

        $event = new DetectUserLanguages($site, $request);

        self::assertEquals($site, $event->getSite());
        self::assertEquals($request, $event->getRequest());
        self::assertEmpty($event->getUserLanguages());

        $userLanguage = ['here', 'is', 'stuff'];

        $event->setUserLanguages($userLanguage);

        self::assertEquals($userLanguage, $event->getUserLanguages());
    }
}
