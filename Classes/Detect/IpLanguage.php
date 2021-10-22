<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Detect;

use LD\LanguageDetection\Event\DetectUserLanguages;
use LD\LanguageDetection\Service\IpLocation;
use LD\LanguageDetection\Service\LanguageService;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Location Service.
 *
 * Get the Location based on the IP.
 * Use the geoplugin.net API.
 */
class IpLanguage
{
    protected IpLocation $ipLocation;
    protected LanguageService $languageService;

    public function __construct(IpLocation $ipLocation, LanguageService $languageService)
    {
        $this->ipLocation = $ipLocation;
        $this->languageService = $languageService;
    }

    public function __invoke(DetectUserLanguages $event): void
    {
        $config = $event->getSite()->getConfiguration();
        $addIp = $config['addIpLocationToBrowserLanguage'] ?? '';
        if (!\in_array($addIp, ['before', 'after', 'replace'])) {
            return;
        }

        $language = $this->getLanguage($event->getRequest());
        if (null === $language) {
            return;
        }

        $base = $event->getUserLanguages();
        switch ($addIp) {
            case 'before':
                array_unshift($base, $language);
                break;
            case 'after':
                $base[] = $language;
                break;
            case 'replace':
                $base = [$language];
                break;
        }
        $event->setUserLanguages($base);
    }

    public function getLanguage(ServerRequestInterface $request): ?string
    {
        $data = $this->ipLocation->get($request->getServerParams()['REMOTE_ADDR'] ?? '');
        if (!isset($data['geoplugin_countryCode'])) {
            return null;
        }

        return $this->languageService->getLanguageByCountry((string)$data['geoplugin_countryCode']);
    }
}
