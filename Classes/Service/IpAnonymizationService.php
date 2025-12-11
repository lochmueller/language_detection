<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Service;

class IpAnonymizationService
{
    public function anonymizeIpAddress(string $ipAddress, int $precision): string
    {
        if ($precision === 4 || filter_var($ipAddress, FILTER_VALIDATE_IP) === false) {
            return $ipAddress;
        }

        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            $octets = explode('.', $ipAddress);
            $zeroOctets = 4 - $precision;

            for ($i = 3; $i >= 0 && $zeroOctets > 0; $i--, $zeroOctets--) {
                $octets[$i] = '0';
            }

            return implode('.', $octets);
        }

        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
            $hextets = explode(':', $ipAddress);
            $zeroHextets = (4 - $precision) * 2;

            for ($i = 7; $i >= 0 && $zeroHextets > 0; $i--, $zeroHextets--) {
                $hextets[$i] = '0000';
            }

            return implode(':', $hextets);
        }

        return $ipAddress;
    }
}
