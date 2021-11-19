<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Service;

use LD\LanguageDetection\Service\IpLocation;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
use TYPO3\CMS\Core\Http\RequestFactory;

/**
 * @internal
 * @coversNothing
 */
class IpLocationTest extends AbstractTest
{
    /**
     * @covers \LD\LanguageDetection\Service\IpLocation
     */
    public function testGetLocationForValidIp(): void
    {
        $locationService = new IpLocation(new RequestFactory());
        $result = $locationService->getCountryCode('8.8.8.8');

        self::assertEquals('US', $result);
    }

    /**
     * @covers \LD\LanguageDetection\Service\IpLocation
     */
    public function testEmptyIpDirectNull(): void
    {
        $locationService = new IpLocation(new RequestFactory());

        self::assertNull($locationService->getCountryCode(''));
    }

    /**
     * @covers \LD\LanguageDetection\Service\IpLocation
     */
    public function testGetLocationForInvalidIp(): void
    {
        $locationService = new IpLocation(new RequestFactory());
        self::assertNull($locationService->getCountryCode('0.0.0.0'));
    }
}
