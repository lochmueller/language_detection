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
 * Use the ip-api.com API.
 */
class GeoPluginDetect
{
    public function __construct(
        protected IpLocation $ipLocation,
        protected LanguageService $languageService,
        protected SiteConfigurationService $siteConfigurationService,
        protected LocaleCollectionSortService $localeCollectionSortService
    ) {}

    public function __invoke(DetectUserLanguagesEvent $event): void
    {
        $addIp = $this->siteConfigurationService->getConfiguration($event->getSite())->getAddIpLocationToBrowserLanguage();
        if (!\in_array($addIp, [LocaleCollectionSortService::SORT_BEFORE, LocaleCollectionSortService::SORT_AFTER, LocaleCollectionSortService::SORT_REPLACE], true)) {
            return;
        }

        $locale = $this->getLocale($event->getRequest());
        if ($locale === null) {
            return;
        }

        $event->setUserLanguages($this->localeCollectionSortService->addLocaleByMode($event->getUserLanguages(), new LocaleValueObject($locale), $addIp));
    }

    public function getLocale(ServerRequestInterface $request): ?string
    {
        $countryCode = $this->ipLocation->getCountryCode($request->getServerParams()['REMOTE_ADDR'] ?? '');
        if ($countryCode === null) {
            return null;
        }

        return $this->languageService->getLanguageByCountry($countryCode) . '_' . $countryCode;
    }
}
