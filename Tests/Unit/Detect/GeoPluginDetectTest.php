<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Detect;

use Lochmueller\LanguageDetection\Detect\GeoPluginDetect;
use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use Lochmueller\LanguageDetection\Service\IpLocation;
use Lochmueller\LanguageDetection\Service\LanguageService;
use Lochmueller\LanguageDetection\Service\LocaleCollectionSortService;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @covers \Lochmueller\LanguageDetection\Detect\GeoPluginDetect
 *
 * @internal
 */
class GeoPluginDetectTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent
     * @covers \Lochmueller\LanguageDetection\Service\LanguageService
     * @covers \Lochmueller\LanguageDetection\Service\LocaleCollectionSortService
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     *
     * @dataProvider data
     *
     * @param string[] $result
     * @param ?string  $ipLocationConfiguration
     */
    public function testAddIpLanguageConfiguration(string $addIpLocationToBrowserLanguage, array $result, ?string $ipLocationConfiguration): void
    {
        $ipLocation = $this->createStub(IpLocation::class);
        $ipLocation->method('getCountryCode')->willReturn($ipLocationConfiguration);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['user-agent' => 'AdsBot-Google']);

        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn(['addIpLocationToBrowserLanguage' => $addIpLocationToBrowserLanguage]);

        $event = new DetectUserLanguagesEvent($site, $serverRequest);
        $event->setUserLanguages(LocaleCollection::fromArray(['default']));

        $ipLanguage = new GeoPluginDetect($ipLocation, new LanguageService(), new SiteConfigurationService(), new LocaleCollectionSortService());
        $ipLanguage($event);

        self::assertSame($result, array_map(fn($locale): string => (string)$locale, $event->getUserLanguages()->toArray()));
    }

    /**
     * @return array<string, array<string|string[]|string|null>>
     */
    public function data(): array
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
