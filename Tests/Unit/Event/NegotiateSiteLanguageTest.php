<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Event;

use LD\LanguageDetection\Event\NegotiateSiteLanguage;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @internal
 * @coversNothing
 */
class NegotiateSiteLanguageTest extends AbstractEventTest
{
    /**
     * @covers \LD\LanguageDetection\Event\NegotiateSiteLanguage
     */
    public function testEventGetterAndSetter(): void
    {
        $site = $this->createMock(SiteInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $userLanguages = ['de-DE'];

        $event = new NegotiateSiteLanguage($site, $request, $userLanguages);

        self::assertEquals($site, $event->getSite());
        self::assertEquals($request, $event->getRequest());
        self::assertEquals($userLanguages, $event->getUserLanguages());
        self::assertNull($event->getSelectedLanguage());

        $selectedLanguage = $this->createMock(SiteLanguage::class);
        $event->setSelectedLanguage($selectedLanguage);

        self::assertEquals($selectedLanguage, $event->getSelectedLanguage());
    }
}
