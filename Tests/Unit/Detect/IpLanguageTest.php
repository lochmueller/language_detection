<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Detect;

use LD\LanguageDetection\Detect\IpLanguage;
use LD\LanguageDetection\Event\DetectUserLanguages;
use LD\LanguageDetection\Service\IpLocation;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @covers \LD\LanguageDetection\Detect\IpLanguage
 *
 * @internal
 */
class IpLanguageTest extends AbstractTest
{
    /**
     * @covers \LD\LanguageDetection\Event\DetectUserLanguages
     *
     * @dataProvider data
     *
     * @param string[]                      $result
     * @param array<string, string>|mixed[] $ipLocationConfiguration
     */
    public function testAddIpLanguageConfiguration(string $addIpLocationToBrowserLanguage, array $result, array $ipLocationConfiguration): void
    {
        $ipLocation = $this->createStub(IpLocation::class);
        $ipLocation->method('get')->willReturn($ipLocationConfiguration);

        $serverRequest = new ServerRequest(null, null, 'php://input', ['user-agent' => 'AdsBot-Google']);

        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn(['addIpLocationToBrowserLanguage' => $addIpLocationToBrowserLanguage]);

        $event = new DetectUserLanguages($site, $serverRequest);
        $event->setUserLanguages(['default']);

        $ipLanguage = new IpLanguage($ipLocation);
        $ipLanguage($event);

        self::assertSame($result, $event->getUserLanguages());
    }

    /**
     * @return array<string, array<string|string[]|string[]>>
     */
    public function data(): array
    {
        return [
            'Empty LD configuration with country result' => ['', ['default'], ['geoplugin_countryCode' => 'DE']],
            'After LD configuration with no country result' => ['after', ['default'], []],
            'After LD configuration with DE country result' => ['after', ['default', 'de'], ['geoplugin_countryCode' => 'DE']],
            'Before LD configuration with DE country result' => ['before', ['de', 'default'], ['geoplugin_countryCode' => 'DE']],
            'Replace LD configuration with DE country result' => ['replace', ['de'], ['geoplugin_countryCode' => 'DE']],
        ];
    }
}
