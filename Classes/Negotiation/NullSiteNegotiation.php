<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Negotiation;

use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent;
use Lochmueller\LanguageDetection\Service\Normalizer;
use Lochmueller\LanguageDetection\Service\SiteCandidateService;
use TYPO3\CMS\Core\Site\Entity\NullSite;

/**
 * Negotiate the language over all sites, if the request does not match any site (NullSite).
 * The user languages are checked in their quality order. Every user language is matched in two
 * steps against all sites, because a locale match (e.g. "de_CH") of any site is better than a
 * language code match (e.g. "de") of the first site.
 */
class NullSiteNegotiation
{
    public function __construct(
        protected Normalizer $normalizer,
        protected SiteCandidateService $siteCandidateService
    ) {}

    public function __invoke(NegotiateSiteLanguageEvent $event): void
    {
        if (!($event->getSite() instanceof NullSite)) {
            return;
        }

        $siteLanguages = $this->siteCandidateService->getCandidateLanguages($event->getRequest());
        if ($siteLanguages === []) {
            return;
        }

        $userLanguages = $this->normalizer->normalizeList($event->getUserLanguages());

        foreach ($userLanguages->toArray() as $userLanguage) {
            foreach ([true, false] as $compareLocale) {
                foreach ($siteLanguages as $siteLanguage) {
                    $siteValue = (string)$siteLanguage->getLocale();
                    $userValue = (string)$userLanguage;

                    if ($compareLocale) {
                        $match = $this->normalizer->normalize($siteValue) === $userValue;
                    } else {
                        $match = $this->normalizer->normalizeToLanguageCode($siteValue) === $this->normalizer->normalizeToLanguageCode($userValue);
                    }

                    if ($match) {
                        $event->setSelectedLanguage($siteLanguage);

                        return;
                    }
                }
            }
        }
    }
}
