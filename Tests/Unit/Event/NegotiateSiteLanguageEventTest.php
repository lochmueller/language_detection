<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Event;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @internal
 * @coversNothing
 */
class NegotiateSiteLanguageEventTest extends AbstractEventTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent
     */
    public function testEventGetterAndSetter(): void
    {
        $site = $this->createMock(SiteInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $userLanguages = LocaleCollection::fromArray(['de-DE']);

        $event = new NegotiateSiteLanguageEvent($site, $request, $userLanguages);

        self::assertEquals($site, $event->getSite());
        self::assertEquals($request, $event->getRequest());
        self::assertEquals($userLanguages, $event->getUserLanguages());
        self::assertNull($event->getSelectedLanguage());

        $selectedLanguage = $this->createMock(SiteLanguage::class);
        $event->setSelectedLanguage($selectedLanguage);

        self::assertEquals($selectedLanguage, $event->getSelectedLanguage());
    }
}
