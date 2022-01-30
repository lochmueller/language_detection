<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Detect;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use Lochmueller\LanguageDetection\Service\IpLocation;
use Lochmueller\LanguageDetection\Service\LanguageService;
use Lochmueller\LanguageDetection\Service\LocaleCollectionSortService;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Location Service.
 *
 * Get the Location based on the IP.
 * Use the geoplugin.net API.
 */
class GeoPluginDetect
{
    protected IpLocation $ipLocation;
    protected LanguageService $languageService;
    protected SiteConfigurationService $siteConfigurationService;
    protected LocaleCollectionSortService $localeCollectionSortService;

    public function __construct(IpLocation $ipLocation, LanguageService $languageService, SiteConfigurationService $siteConfigurationService, LocaleCollectionSortService $localeCollectionSortService)
    {
        $this->ipLocation = $ipLocation;
        $this->languageService = $languageService;
        $this->siteConfigurationService = $siteConfigurationService;
        $this->localeCollectionSortService = $localeCollectionSortService;
    }

    public function __invoke(DetectUserLanguagesEvent $event): void
    {
        $addIp = $this->siteConfigurationService->getConfiguration($event->getSite())->getAddIpLocationToBrowserLanguage();
        if (!\in_array($addIp, [LocaleCollectionSortService::SORT_BEFORE, LocaleCollectionSortService::SORT_AFTER, LocaleCollectionSortService::SORT_REPLACE])) {
            return;
        }

        // @todo move to sort option
        $language = $this->getLanguage($event->getRequest());
        if (null === $language) {
            return;
        }

        $base = $event->getUserLanguages()->toArray();
        switch ($addIp) {
            case LocaleCollectionSortService::SORT_BEFORE:
                array_unshift($base, $language);
                break;
            case LocaleCollectionSortService::SORT_AFTER:
                $base[] = $language;
                break;
            case LocaleCollectionSortService::SORT_REPLACE:
                $base = [$language];
                break;
        }
        $event->setUserLanguages(LocaleCollection::fromArray(array_map(fn ($item): string => (string)$item, $base)));
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
