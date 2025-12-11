<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Service\IpAnonymizationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;

/**
 * @internal
 *
 * @coversNothing
 */
class IpAnonymizationServiceTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Service\IpAnonymizationService
     */
    public function testIpAddressAnonymizationForIpV4(): void
    {
        $ipAnonymizationService = new IpAnonymizationService();
        $ipv4 = '192.168.12.34';

        self::assertSame('192.168.12.34', $ipAnonymizationService->anonymizeIpAddress($ipv4, 4));
        self::assertSame('192.168.12.0', $ipAnonymizationService->anonymizeIpAddress($ipv4, 3));
        self::assertSame('192.168.0.0', $ipAnonymizationService->anonymizeIpAddress($ipv4, 2));
        self::assertSame('192.0.0.0', $ipAnonymizationService->anonymizeIpAddress($ipv4, 1));
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Service\IpAnonymizationService
     */
    public function testIpAddressAnonymizationForIpV6(): void
    {
        $ipAnonymizationService = new IpAnonymizationService();
        $ipv6 = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';

        self::assertSame('2001:0db8:85a3:0000:0000:8a2e:0370:7334', $ipAnonymizationService->anonymizeIpAddress($ipv6, 4));
        self::assertSame('2001:0db8:85a3:0000:0000:8a2e:0000:0000', $ipAnonymizationService->anonymizeIpAddress($ipv6, 3));
        self::assertSame('2001:0db8:85a3:0000:0000:0000:0000:0000', $ipAnonymizationService->anonymizeIpAddress($ipv6, 2));
        self::assertSame('2001:0db8:0000:0000:0000:0000:0000:0000', $ipAnonymizationService->anonymizeIpAddress($ipv6, 1));
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Service\IpAnonymizationService
     */
    public function testIpAddressAnonymizationForInvalidIp(): void
    {
        $ipAnonymizationService = new IpAnonymizationService();

        self::assertSame('not-an-ip', $ipAnonymizationService->anonymizeIpAddress('not-an-ip', 3));
    }
}
