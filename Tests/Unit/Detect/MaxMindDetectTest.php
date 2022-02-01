<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Detect;

use Lochmueller\LanguageDetection\Detect\MaxMindDetect;
use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use Lochmueller\LanguageDetection\Service\LanguageService;
use Lochmueller\LanguageDetection\Service\LocaleCollectionSortService;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @covers \Lochmueller\LanguageDetection\Detect\MaxMindDetect
 *
 * @internal
 */
class MaxMindDetectTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent
     * @covers \Lochmueller\LanguageDetection\Service\LanguageService
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testNoProviderConfiguration(): void
    {
        $serverRequest = new ServerRequest(null, null, 'php://input', []);

        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn([]);

        $event = new DetectUserLanguagesEvent($site, $serverRequest);
        $event->setUserLanguages(LocaleCollection::fromArray(['default']));

        $maxMindDetect = new MaxMindDetect(new LanguageService(), new SiteConfigurationService(), new LocaleCollectionSortService());
        $maxMindDetect($event);

        self::assertCount(1, $event->getUserLanguages()->toArray());
    }

    public function testMemoryConsumptionAndExecutionTimeOfMaxMindDatabaseFileUploadInclSiteConfigurationsStructure(): void
    {
        $serverRequest = new ServerRequest(
            null,
            null,
            'php://input',
            [],
            [
                'REMOTE_ADDR' => '172.217.0.0', // Google services in US
            ]
        );

        $dbFile = \dirname(__FILE__, 3) . '/Fixtures/GeoIP2-Country.mmdb';
        if (!is_file($dbFile)) {
            self::markTestIncomplete('No local GEO IP 2 database is found');
        }

        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn([
            'languageDetectionMaxMindDatabasePath' => $dbFile,
        ]);

        $event = new DetectUserLanguagesEvent($site, $serverRequest);
        $event->setUserLanguages(LocaleCollection::fromArray([]));

        $beforeMemory = memory_get_usage();
        $beforeTime = microtime(true);

        $maxMindDetect = new MaxMindDetect(new LanguageService(), new SiteConfigurationService(), new LocaleCollectionSortService());
        $maxMindDetect($event);

        $memoryUsage = memory_get_usage() - $beforeMemory;
        $timeUsage = microtime(true) - $beforeTime;

        self::assertLessThanOrEqual(1024 * 200, $memoryUsage);
        self::assertLessThanOrEqual(0.02, $timeUsage);
        $languages = $event->getUserLanguages()->toArray();
        self::assertCount(1, $languages);
        self::assertEquals('en_US', (string)$languages[0]);
    }
}
