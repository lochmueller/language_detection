<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Event;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject;
use Lochmueller\LanguageDetection\Event\DetectUserLanguages;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 * @coversNothing
 */
class DetectUserLanguagesTest extends AbstractEventTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguages
     */
    public function testEventGetterAndSetter(): void
    {
        $site = $this->createMock(SiteInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);

        $event = new DetectUserLanguages($site, $request);

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
