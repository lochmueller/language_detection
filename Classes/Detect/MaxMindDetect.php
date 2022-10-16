<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Detect;

use GeoIp2\Database\Reader;
use GeoIp2\ProviderInterface;
use GeoIp2\WebService\Client;
use Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject;
use Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration;
use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use Lochmueller\LanguageDetection\Service\LanguageService;
use Lochmueller\LanguageDetection\Service\LocaleCollectionSortService;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
        $configuration = $this->siteConfigurationService->getConfiguration($event->getSite());
        $provider = $this->getProvider($configuration);

        if (!$provider instanceof ProviderInterface) {
            return;
        }

        try {
            $result = $provider->country($event->getRequest()->getServerParams()['REMOTE_ADDR'] ?? '');
        } catch (\Exception $exception) {
            return;
        }
        $locale = $this->languageService->getLanguageByCountry((string)$result->country->isoCode) . '_' . $result->country->isoCode;
        $event->setUserLanguages($this->localeCollectionSortService->addLocaleByMode($event->getUserLanguages(), new LocaleValueObject($locale)));
    }

    protected function getProvider(SiteConfiguration $siteConfiguration): ?ProviderInterface
    {
        if (!interface_exists(ProviderInterface::class)) {
            return null;
        }

        if ($siteConfiguration->getMaxMindAccountId() && $siteConfiguration->getMaxMindLicenseKey()) {
            return new Client($siteConfiguration->getMaxMindAccountId(), $siteConfiguration->getMaxMindLicenseKey());
        }

        if ($siteConfiguration->getMaxMindDatabasePath() !== '') {
            $dbPath = GeneralUtility::getFileAbsFileName($siteConfiguration->getMaxMindDatabasePath());
            if (is_file($dbPath)) {
                return new Reader($dbPath);
            }
        }

        return null;
    }
}
