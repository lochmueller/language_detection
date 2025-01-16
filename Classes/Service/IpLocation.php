<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Service;

use Psr\Http\Message\RequestFactoryInterface;
use TYPO3\CMS\Core\Http\Client\GuzzleClientFactory;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class IpLocation
{
    public function __construct(private RequestFactoryInterface $requestFactory) {}

    public function getCountryCode(string $ip): ?string
    {
        if ($ip === '') {
            return null;
        }
        $urlService = 'http://ip-api.com/json/' . $ip;
        try {
            $request = $this->requestFactory->createRequest('GET', $urlService);
            $response = $this->getClient()->send($request);

            if ($response->getStatusCode() !== 200) {
                throw new IpLocationException('Missing information in response', 123781);
            }

            $result = \json_decode((string)$response->getBody());

            if (!($result instanceof \stdClass) || $result->status !== 'success') {
                throw new IpLocationException('No valid data', 162378);
            }

            return $result->countryCode ?? null;
        } catch (IpLocationException) {
            return null;
        }
    }

    protected function getClient(): \Psr\Http\Client\ClientInterface
    {
        return GeneralUtility::makeInstance(GuzzleClientFactory::class)->getClient();
    }
}
