<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Detect;

use Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject;
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

        $language = $this->getLanguage($event->getRequest());
        if ($language === null) {
            return;
        }

        $event->setUserLanguages($this->localeCollectionSortService->addLocaleByMode($event->getUserLanguages(), new LocaleValueObject($language), $addIp));
    }

    public function getLanguage(ServerRequestInterface $request): ?string
    {
        $countryCode = $this->ipLocation->getCountryCode($request->getServerParams()['REMOTE_ADDR'] ?? '');
        if ($countryCode === null) {
            return null;
        }

        return $this->languageService->getLanguageByCountry($countryCode);
    }
}
