<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Event;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent::class)]
class NegotiateSiteLanguageEventTest extends AbstractEventTest
{
    public function testEventGetterAndSetter(): void
    {
        $site = self::createStub(SiteInterface::class);
        $request = self::createStub(ServerRequestInterface::class);
        $userLanguages = LocaleCollection::fromArray(['de-DE']);

        $event = new NegotiateSiteLanguageEvent($site, $request, $userLanguages);

        self::assertEquals($site, $event->getSite());
        self::assertEquals($request, $event->getRequest());
        self::assertEquals($userLanguages, $event->getUserLanguages());
        self::assertNull($event->getSelectedLanguage());

        $selectedLanguage = self::createStub(SiteLanguage::class);
        $event->setSelectedLanguage($selectedLanguage);

        self::assertEquals($selectedLanguage, $event->getSelectedLanguage());
    }
}
