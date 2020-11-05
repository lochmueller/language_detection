<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Negotiation;

use LD\LanguageDetection\Event\NegotiateSiteLanguage;
use LD\LanguageDetection\Service\Normalizer;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

class DefaultNegotiation
{
    protected Normalizer $normalizer;

    public function __construct(Normalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function __invoke(NegotiateSiteLanguage $event): void
    {
        if (null !== $event->getSelectedLanguage()) {
            // Another event has already negotiate the language
            return;
        }

        $compareWith = [
            'getLocale',
            'getTwoLetterIsoCode',
        ];
        $userLanguages = $this->normalizer->normalizeList($event->getUserLanguages());
        foreach ($compareWith as $function) {
            foreach ($userLanguages as $userLanguage) {
                foreach ($event->getSite()->getAllLanguages() as $siteLanguage) {
                    /** @var SiteLanguage $siteLanguage */
                    if ($siteLanguage->enabled() && $userLanguage === $this->normalizer->normalize((string)$siteLanguage->{$function}())) {
                        $event->setSelectedLanguage($siteLanguage);

                        return;
                    }
                }
            }
        }
    }
}
