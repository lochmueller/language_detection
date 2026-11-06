<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Detect;

use Lochmueller\LanguageDetection\Detect\GeoPluginDetect;
use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use Lochmueller\LanguageDetection\Service\IpAnonymizationService;
use Lochmueller\LanguageDetection\Service\IpLocation;
use Lochmueller\LanguageDetection\Service\LanguageService;
use Lochmueller\LanguageDetection\Service\LocaleCollectionSortService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Detect\GeoPluginDetect::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\LanguageService::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\LocaleCollectionSortService::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\SiteConfigurationService::class)]
class GeoPluginDetectTest extends AbstractUnitTest
{
    /**
     * @param string[] $result
     * @param ?string  $ipLocationConfiguration
     */
    #[DataProvider('data')]
    public function testAddIpLanguageConfiguration(string $addIpLocationToBrowserLanguage, array $result, ?string $ipLocationConfiguration): void
    {
        self::skipWithoutLikelySubtags();

        $ipLocation = self::createStub(IpLocation::class);
        $ipLocation->method('getCountryCode')->willReturn($ipLocationConfiguration);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['user-agent' => 'AdsBot-Google']);

        $site = self::createStub(Site::class);
        $site->method('getConfiguration')->willReturn(['addIpLocationToBrowserLanguage' => $addIpLocationToBrowserLanguage]);

        $event = new DetectUserLanguagesEvent($site, $serverRequest);
        $event->setUserLanguages(LocaleCollection::fromArray(['default']));

        $ipLanguage = new GeoPluginDetect(new IpAnonymizationService(), $ipLocation, new LanguageService(), $this->getSiteConfigurationService(), new LocaleCollectionSortService());
        $ipLanguage($event);

        self::assertSame($result, array_map(fn($locale): string => (string)$locale, $event->getUserLanguages()->toArray()));
    }

    /**
     * @return array<string, array<string|string[]|string|null>>
     */
    public static function data(): array
    {
        return [
            'Empty LD configuration with country result' => ['', ['default'], 'DE'],
            'After LD configuration with no country result' => ['after', ['default'], null],
            'After LD configuration with DE country result' => ['after', ['default', 'de_DE'], 'DE'],
            'Before LD configuration with DE country result' => ['before', ['de_DE', 'default'], 'DE'],
            'Replace LD configuration with DE country result' => ['replace', ['de_DE'], 'DE'],
            'Wrong LD configuration with DE country result' => ['wrong', ['default'], 'DE'],
        ];
    }
}
