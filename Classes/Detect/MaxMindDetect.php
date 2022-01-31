<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Detect;

use GeoIp2\Database\Reader;
use GeoIp2\ProviderInterface;
use GeoIp2\WebService\Client;
use Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject;
use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use Lochmueller\LanguageDetection\Service\LanguageService;
use Lochmueller\LanguageDetection\Service\LocaleCollectionSortService;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;

class MaxMindDetect
{
    protected LanguageService $languageService;
    protected SiteConfigurationService $siteConfigurationService;
    protected LocaleCollectionSortService $localeCollectionSortService;

    public function __construct(LanguageService $languageService, SiteConfigurationService $siteConfigurationService, LocaleCollectionSortService $localeCollectionSortService)
    {
        $this->languageService = $languageService;
        $this->siteConfigurationService = $siteConfigurationService;
        $this->localeCollectionSortService = $localeCollectionSortService;
    }

    public function __invoke(DetectUserLanguagesEvent $event): void
    {
        $provider = $this->getProvider();
        if (!$provider instanceof ProviderInterface) {
            return;
        }

        try {
            $result = $provider->country($event->getRequest()->getServerParams()['REMOTE_ADDR'] ?? '');
        } catch (\Exception $exception) {
            return;
        }
        $locale = $this->languageService->getLanguageByCountry($result->country->isoCode) . '_' . $result->country->isoCode;
        $event->setUserLanguages($this->localeCollectionSortService->addLocaleByMode($event->getUserLanguages(), new LocaleValueObject($locale)));
    }

    protected function getProvider(): ?ProviderInterface
    {
        if (!class_exists(ProviderInterface::class)) {
            return null;
        }

        // @todo build up via configuration and Reader or a Client Object

        //$reader = new Reader('/usr/local/share/GeoIP/GeoIP2-City.mmdb');

        // $client = new Client(42, 'abcdef123456');

        return null;
    }
}
