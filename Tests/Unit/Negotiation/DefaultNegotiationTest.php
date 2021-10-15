<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Negotiation;

use LD\LanguageDetection\Event\NegotiateSiteLanguage;
use LD\LanguageDetection\Negotiation\DefaultNegotiation;
use LD\LanguageDetection\Service\Normalizer;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
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
     * @covers \LD\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \LD\LanguageDetection\Service\Normalizer
     */
    public function testNoLanguagesWithEmptyUserLanguages(): void
    {
        $negotiation = new DefaultNegotiation(new Normalizer());

        $site = $this->createMock(Site::class);

        $event = new NegotiateSiteLanguage($site, $this->createMock(ServerRequestInterface::class), []);
        $negotiation($event);

        self::assertNull($event->getSelectedLanguage());
    }

    /**
     * @covers \LD\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \LD\LanguageDetection\Service\Normalizer
     */
    public function testSelectedLanguagesWithUserLanguages(): void
    {
        $negotiation = new DefaultNegotiation(new Normalizer());

        $en = new SiteLanguage(0, 'en_GB', new Uri('/en/'), ['enabled' => true]);
        $de = new SiteLanguage(1, 'de_DE', new Uri('/en/'), ['enabled' => true]);

        $site = $this->createStub(Site::class);
        $site->method('getAllLanguages')->willReturn([$en, $de]);

        $event = new NegotiateSiteLanguage($site, $this->createMock(ServerRequestInterface::class), ['de_DE', 'en_GB']);
        $negotiation($event);

        self::assertSame($de, $event->getSelectedLanguage());
    }
}
