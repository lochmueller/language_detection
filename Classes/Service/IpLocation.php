<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Service;

use Psr\Http\Message\RequestFactoryInterface;
use TYPO3\CMS\Core\Http\Client\GuzzleClientFactory;

class IpLocation
{
    private RequestFactoryInterface $requestFactory;

    public function __construct(RequestFactoryInterface $requestFactory)
    {
        $this->requestFactory = $requestFactory;
    }

    public function getCountryCode(string $ip): ?string
    {
        if ('' === $ip) {
            return null;
        }
        $urlService = 'http://www.geoplugin.net/php.gp?ip=' . $ip;
        try {
            $request = $this->requestFactory->createRequest('GET', $urlService);
            $response = GuzzleClientFactory::getClient()->send($request);

            if (200 !== $response->getStatusCode()) {
                throw new IpLocationException('Missing information in response', 123781);
            }
            $result = (array)unserialize((string)$response->getBody(), ['allowed_classes' => false]);

            if (empty($result) || 404 === (int)$result['geoplugin_status']) {
                throw new IpLocationException('No valid data', 162378);
            }

            return $result['geoplugin_countryCode'] ?? null;
        } catch (IpLocationException $exc) {
            return null;
        }
    }
}
