<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Negotiation;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent;
use Lochmueller\LanguageDetection\Negotiation\NullSiteNegotiation;
use Lochmueller\LanguageDetection\Service\Normalizer;
use Lochmueller\LanguageDetection\Service\SiteCandidateService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\GlobalConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Negotiation\NullSiteNegotiation::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\GlobalConfigurationService::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\Normalizer::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\SiteCandidateService::class)]
class NullSiteNegotiationTest extends AbstractUnitTest
{
    public function testRegularSiteIsNotTouched(): void
    {
        $event = new NegotiateSiteLanguageEvent(
            self::createStub(Site::class),
            new ServerRequest('https://www.example.com/'),
            LocaleCollection::fromArray(['de-CH'])
        );

        $this->getNegotiation()($event);

        self::assertNull($event->getSelectedLanguage());
    }

    /**
     * @param string[] $userLanguages
     */
    #[DataProvider('userLanguageData')]
    public function testNegotiationOverAllSites(array $userLanguages, ?string $expectedBase): void
    {
        $event = new NegotiateSiteLanguageEvent(
            new NullSite(),
            new ServerRequest('https://www.example.com/'),
            LocaleCollection::fromArray($userLanguages)
        );

        $this->getNegotiation()($event);

        $selectedLanguage = $event->getSelectedLanguage();
        if ($expectedBase === null) {
            self::assertNull($selectedLanguage);

            return;
        }

        self::assertInstanceOf(SiteLanguage::class, $selectedLanguage);
        self::assertEquals($expectedBase, (string)$selectedLanguage->getBase());
    }

    /**
     * @return array<string, mixed[]>
     */
    public static function userLanguageData(): array
    {
        return [
            'Exact locale of another site wins over the language code of the first site' => [['de-CH', 'de', 'en'], 'https://www.example.com/ch/de/'],
            'Exact locale of the first site' => [['de-DE'], 'https://www.example.com/de/'],
            'Language code fallback' => [['fr'], 'https://www.example.com/ch/fr/'],
            'Plain language code uses the first matching site' => [['de'], 'https://www.example.com/de/'],
            'Quality order of the user languages is respected' => [['fr-BE', 'de-CH'], 'https://www.example.com/ch/fr/'],
            'No match at all' => [['es'], null],
            'Without user languages' => [[], null],
        ];
    }

    public function testNoCandidateLanguages(): void
    {
        $event = new NegotiateSiteLanguageEvent(
            new NullSite(),
            new ServerRequest('https://www.unknown.com/'),
            LocaleCollection::fromArray(['de-DE'])
        );

        $this->getNegotiation()($event);

        self::assertNull($event->getSelectedLanguage());
    }

    protected function getNegotiation(): NullSiteNegotiation
    {
        $de = new Site('de', 1, [
            'base' => 'https://www.example.com/de/',
            'languages' => [
                ['languageId' => 0, 'base' => '/', 'locale' => 'de_DE'],
            ],
        ]);
        $ch = new Site('ch', 2, [
            'base' => 'https://www.example.com/ch/',
            'languages' => [
                ['languageId' => 0, 'base' => '/de/', 'locale' => 'de_CH'],
                ['languageId' => 1, 'base' => '/fr/', 'locale' => 'fr_CH'],
            ],
        ]);

        $siteFinder = self::createStub(SiteFinder::class);
        $siteFinder->method('getAllSites')->willReturn(['de' => $de, 'ch' => $ch]);

        return new NullSiteNegotiation(
            new Normalizer(),
            new SiteCandidateService($siteFinder, $this->getGlobalConfigurationService(['enableNullSiteDetection' => '1']))
        );
    }
}
