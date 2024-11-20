<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Negotiation;

use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent;
use Lochmueller\LanguageDetection\Service\Normalizer;

class DefaultNegotiation
{
    public function __construct(protected Normalizer $normalizer) {}

    public function __invoke(NegotiateSiteLanguageEvent $event): void
    {
        $userLanguages = $this->normalizer->normalizeList($event->getUserLanguages());
        foreach ($userLanguages->toArray() as $userLanguage) {
            foreach ($event->getSite()->getLanguages() as $siteLanguage) {
                $compareWith = [
                    (string)$siteLanguage->getLocale(),
                    $siteLanguage->getLocale()->getLanguageCode(),
                ];
                foreach ($compareWith as $value) {
                    $config = $siteLanguage->toArray();
                    if ($siteLanguage->enabled()
                        && ($config['excludeFromLanguageDetection'] ?? false) !== true
                        && (string)$userLanguage === $this->normalizer->normalize($value)
                    ) {
                        $event->setSelectedLanguage($siteLanguage);

                        return;
                    }
                }
            }
        }
    }
}
