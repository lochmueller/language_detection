<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Detect;

use Lochmueller\LanguageDetection\Detect\IpLanguage;
use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Event\DetectUserLanguages;
use Lochmueller\LanguageDetection\Service\IpLocation;
use Lochmueller\LanguageDetection\Service\LanguageService;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractTest;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @covers \Lochmueller\LanguageDetection\Detect\IpLanguage
 *
 * @internal
 */
class IpLanguageTest extends AbstractTest
{
    /**
     * @covers       \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguages
     * @covers \Lochmueller\LanguageDetection\Service\LanguageService
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

        $event = new DetectUserLanguages($site, $serverRequest);
        $event->setUserLanguages(LocaleCollection::fromArray(['default']));

        $ipLanguage = new IpLanguage($ipLocation, new LanguageService(), new SiteConfigurationService());
        $ipLanguage($event);

        self::assertSame($result, array_map(fn ($locale): string => (string)$locale, $event->getUserLanguages()->toArray()));
    }

    /**
     * @return array<string, array<string|string[]|string|null>>
     */
    public function data(): array
    {
        return [
            'Empty LD configuration with country result' => ['', ['default'], 'DE'],
            'After LD configuration with no country result' => ['after', ['default'], null],
            'After LD configuration with DE country result' => ['after', ['default', 'de'], 'DE'],
            'Before LD configuration with DE country result' => ['before', ['de', 'default'], 'DE'],
            'Replace LD configuration with DE country result' => ['replace', ['de'], 'DE'],
            'Wrong LD configuration with DE country result' => ['wrong', ['default'], 'DE'],
        ];
    }
}
