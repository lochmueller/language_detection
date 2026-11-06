<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Service;

use Psr\Http\Message\RequestFactoryInterface;
use TYPO3\CMS\Core\Http\Client\GuzzleClientFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
class IpLocation
{
    private const LIFETIME_SUCCESS = 86400; // 24h
    private const LIFETIME_FAILURE = 600;   // 10min
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        #[Autowire(service: 'cache.language_detection_ip_location')]
        private FrontendInterface $ipLocationCache,
    ) {}

    public function getCountryCode(string $ip): ?string
    {
        if ($ip === '') return null;

        // IPs contain "." / ":" which are not allowed in cache identifiers
        $cacheIdentifier = sha1($ip);

        $cached = $this->ipLocationCache->get($cacheIdentifier);
        if (is_array($cached)) return $cached['countryCode']; // may be null (cached failure)

        $countryCode = $this->fetchCountryCode($ip);

        $this->ipLocationCache->set(
            $cacheIdentifier,
            ['countryCode' => $countryCode],
            [],
            $countryCode === null ? self::LIFETIME_FAILURE : self::LIFETIME_SUCCESS
        );

        return $countryCode;
    }

    protected function fetchCountryCode(string $ip): ?string
    {
        try {
            $request = $this->requestFactory->createRequest('GET', 'http://ip-api.com/json/' . $ip);
            $response = $this->getClient()->send($request, ['http_errors' => false]);

            if ($response->getStatusCode() !== 200)
                throw new IpLocationException('Missing information in response', 123781);

            $result = \json_decode((string)$response->getBody());

            if (!($result instanceof \stdClass) || $result->status !== 'success')
                throw new IpLocationException('No valid data', 162378);

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
