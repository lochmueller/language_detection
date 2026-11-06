<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Negotiation;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent;
use Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation;
use Lochmueller\LanguageDetection\Service\Normalizer;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\Normalizer::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject::class)]
class DefaultNegotiationTest extends AbstractUnitTest
{
    public function testNoLanguagesWithEmptyUserLanguages(): void
    {
        $negotiation = new DefaultNegotiation(new Normalizer());

        $site = self::createStub(Site::class);

        $event = new NegotiateSiteLanguageEvent($site, self::createStub(ServerRequestInterface::class), LocaleCollection::fromArray([]));
        $negotiation($event);

        self::assertNull($event->getSelectedLanguage());
    }

    public function testSelectedLanguagesWithUserLanguages(): void
    {
        $negotiation = new DefaultNegotiation(new Normalizer());

        $en = new SiteLanguage(0, 'en_GB', new Uri('/en/'), ['enabled' => true]);
        $de = new SiteLanguage(1, 'de_DE', new Uri('/en/'), ['enabled' => true]);

        $site = self::createStub(Site::class);
        $site->method('getLanguages')->willReturn([$en, $de]);

        $event = new NegotiateSiteLanguageEvent($site, self::createStub(ServerRequestInterface::class), LocaleCollection::fromArray(['de_DE', 'en_GB']));
        $negotiation($event);

        self::assertSame($de, $event->getSelectedLanguage());
    }
}
