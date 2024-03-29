<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit;

use TYPO3\CMS\Core\Http\Client\GuzzleClientFactory;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Information\Typo3Version;
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

    public function getRequestFactory(): RequestFactory
    {
        if ((new Typo3Version())->getMajorVersion() >= 12) {
            return new RequestFactory(new GuzzleClientFactory());
        }
        return new RequestFactory();
    }
}
