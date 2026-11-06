<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit;

use Lochmueller\LanguageDetection\Service\GlobalConfigurationService;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\Frontend\NullFrontend;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\Client\GuzzleClientFactory;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

abstract class AbstractUnitTest extends UnitTestCase
{
    public static function assertExecutionTimeLessThenOrEqual(float $timeInSeconds, callable $workload): void
    {
        $beforeTime = microtime(true);
        $workload();
        $timeUsage = microtime(true) - $beforeTime;

        self::assertLessThanOrEqual($timeInSeconds, $timeUsage, 'Execution time of this workload should be less then ' . $timeInSeconds . ' seconds.');
    }

    public static function assertExecutionMemoryLessThenOrEqual(float $memoryInKb, callable $workload): void
    {
        $beforeMemory = memory_get_usage();
        $workload();
        $memoryUsage = memory_get_usage() - $beforeMemory;
        self::assertLessThanOrEqual(1024 * $memoryInKb, $memoryUsage, 'Execution memory of this workload should be less then ' . $memoryInKb . 'KB.');
    }

    public static function skipWithoutLikelySubtags(): void
    {
        if (!\ResourceBundle::create('likelySubtags', 'ICUDATA', false) instanceof \ResourceBundle) {
            self::markTestSkipped('The ICU "likelySubtags" resource bundle is not available in this environment.');
        }
    }

    public function getRequestFactory(): RequestFactory
    {
        return new RequestFactory(new GuzzleClientFactory());
    }

    public function getNullCache(string $identifier = 'test'): FrontendInterface
    {
        return new NullFrontend($identifier);
    }

    /**
     * @param array<string, mixed> $globalConfiguration
     */
    public function getGlobalConfigurationService(array $globalConfiguration = []): GlobalConfigurationService
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][GlobalConfigurationService::EXTENSION_KEY] = $globalConfiguration;

        return new GlobalConfigurationService(new ExtensionConfiguration());
    }

    /**
     * @param array<string, mixed> $globalConfiguration
     */
    public function getSiteConfigurationService(array $globalConfiguration = []): SiteConfigurationService
    {
        return new SiteConfigurationService($this->getGlobalConfigurationService($globalConfiguration));
    }
}
