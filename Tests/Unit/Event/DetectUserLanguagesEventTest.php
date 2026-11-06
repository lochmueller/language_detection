<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Event;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject;
use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent::class)]
class DetectUserLanguagesEventTest extends AbstractEventTest
{
    public function testEventGetterAndSetter(): void
    {
        $site = self::createStub(SiteInterface::class);
        $request = self::createStub(ServerRequestInterface::class);

        $event = new DetectUserLanguagesEvent($site, $request);

        self::assertEquals($site, $event->getSite());
        self::assertEquals($request, $event->getRequest());
        self::assertTrue($event->getUserLanguages()->isEmpty());

        $userLanguage = LocaleCollection::fromArray(['here', 'is', 'stuff']);

        $event->setUserLanguages($userLanguage);

        self::assertEquals($userLanguage, $event->getUserLanguages());

        $event->addUserLanguage(new LocaleValueObject('neu'));
        self::assertEquals(LocaleCollection::fromArray(['here', 'is', 'stuff', 'neu']), $event->getUserLanguages());
    }
}
