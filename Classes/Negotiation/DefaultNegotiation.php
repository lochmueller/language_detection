<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Negotiation;

use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage;
use Lochmueller\LanguageDetection\Service\Normalizer;

class DefaultNegotiation
{
    protected Normalizer $normalizer;

    public function __construct(Normalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function __invoke(NegotiateSiteLanguage $event): void
    {
        $compareWith = [
            'getLocale',
            'getTwoLetterIsoCode',
        ];
        $userLanguages = $this->normalizer->normalizeList($event->getUserLanguages());
        foreach ($userLanguages->toArray() as $userLanguage) {
            foreach ($event->getSite()->getLanguages() as $siteLanguage) {
                foreach ($compareWith as $function) {
                    if ($siteLanguage->enabled() && (string)$userLanguage === $this->normalizer->normalize((string)$siteLanguage->{$function}())) {
                        $event->setSelectedLanguage($siteLanguage);

                        return;
                    }
                }
            }
        }
    }
}
