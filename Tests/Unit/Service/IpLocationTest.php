<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Service\IpLocation;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Service\IpLocation::class)]
class IpLocationTest extends AbstractUnitTest
{
    protected function setUp(): void
    {
        $this->resetSingletonInstances = true;
        parent::setUp();
    }

    public function testGetLocationForValidIp(): void
    {
        $locationService = new IpLocation($this->getRequestFactory(), $this->getNullCache('language_detection_ip_location'));
        $result = $locationService->getCountryCode('8.8.8.8');

        self::assertEquals('US', $result);
    }

    public function testEmptyIpDirectNull(): void
    {
        $locationService = new IpLocation($this->getRequestFactory(), $this->getNullCache('language_detection_ip_location'));

        self::assertNull($locationService->getCountryCode(''));
    }

    public function testGetLocationForInvalidIp(): void
    {
        $locationService = new IpLocation($this->getRequestFactory(), $this->getNullCache('language_detection_ip_location'));
        self::assertNull($locationService->getCountryCode('0.0.0.0'));
    }
}
