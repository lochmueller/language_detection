<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use Psr\Http\Message\RequestFactoryInterface;

class IpLocation
{
    private RequestFactoryInterface $requestFactory;

    public function __construct(RequestFactoryInterface $requestFactory)
    {
        $this->requestFactory = $requestFactory;
    }

    public function get(string $ip): ?array
    {
        if ('' === $ip) {
            return null;
        }
        $urlService = 'http://www.geoplugin.net/php.gp?ip=' . $ip;
        try {
            $response = $this->requestFactory->request($urlService);

            if (200 !== $response->getStatusCode()) {
                throw new IpLocationException('Missing information in response', 123781);
            }
            $result = (array)unserialize((string)$response->getBody());

            if (empty($result) || 404 === (int)$result['geoplugin_status']) {
                throw new IpLocationException('No valid data', 162378);
            }

            return $result;
        } catch (IpLocationException $exc) {
            return null;
        }
    }
}
