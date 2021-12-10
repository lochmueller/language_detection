<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Negotiation;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage;
use Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation;
use Lochmueller\LanguageDetection\Service\Normalizer;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractTest;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @internal
 * @coversNothing
 */
class DefaultNegotiationTest extends AbstractTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Service\Normalizer
     */
    public function testNoLanguagesWithEmptyUserLanguages(): void
    {
        $negotiation = new DefaultNegotiation(new Normalizer());

        $site = $this->createMock(Site::class);

        $event = new NegotiateSiteLanguage($site, $this->createMock(ServerRequestInterface::class), LocaleCollection::fromArray([]));
        $negotiation($event);

        self::assertNull($event->getSelectedLanguage());
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Service\Normalizer
     */
    public function testSelectedLanguagesWithUserLanguages(): void
    {
        $negotiation = new DefaultNegotiation(new Normalizer());

        $en = new SiteLanguage(0, 'en_GB', new Uri('/en/'), ['enabled' => true]);
        $de = new SiteLanguage(1, 'de_DE', new Uri('/en/'), ['enabled' => true]);

        $site = $this->createStub(Site::class);
        $site->method('getLanguages')->willReturn([$en, $de]);

        $event = new NegotiateSiteLanguage($site, $this->createMock(ServerRequestInterface::class), LocaleCollection::fromArray(['de_DE', 'en_GB']));
        $negotiation($event);

        self::assertSame($de, $event->getSelectedLanguage());
    }
}
