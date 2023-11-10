<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Service\IpLocation;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;

/**
 * @internal
 *
 * @coversNothing
 */
class IpLocationTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Service\IpLocation
     */
    public function testGetLocationForValidIp(): void
    {
        $locationService = new IpLocation($this->getRequestFactory());
        $result = $locationService->getCountryCode('8.8.8.8');

        self::assertEquals('US', $result);
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Service\IpLocation
     */
    public function testEmptyIpDirectNull(): void
    {
        $locationService = new IpLocation($this->getRequestFactory());

        self::assertNull($locationService->getCountryCode(''));
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Service\IpLocation
     */
    public function testGetLocationForInvalidIp(): void
    {
        $locationService = new IpLocation($this->getRequestFactory());
        self::assertNull($locationService->getCountryCode('0.0.0.0'));
    }
}
