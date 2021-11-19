<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Detect;

use LD\LanguageDetection\Event\DetectUserLanguages;
use LD\LanguageDetection\Service\IpLocation;
use LD\LanguageDetection\Service\LanguageService;
use LD\LanguageDetection\Service\SiteConfigurationService;
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
    protected SiteConfigurationService $siteConfigurationService;

    public function __construct(IpLocation $ipLocation, LanguageService $languageService, SiteConfigurationService $siteConfigurationService)
    {
        $this->ipLocation = $ipLocation;
        $this->languageService = $languageService;
        $this->siteConfigurationService = $siteConfigurationService;
    }

    public function __invoke(DetectUserLanguages $event): void
    {
        $addIp = $this->siteConfigurationService->getConfiguration($event->getSite())->getAddIpLocationToBrowserLanguage();
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
        $countryCode = $this->ipLocation->getCountryCode($request->getServerParams()['REMOTE_ADDR'] ?? '');
        if (null === $countryCode) {
            return null;
        }

        return $this->languageService->getLanguageByCountry($countryCode);
    }
}
