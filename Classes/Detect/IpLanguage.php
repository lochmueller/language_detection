<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Detect;

use LD\LanguageDetection\Event\DetectUserLanguages;
use LD\LanguageDetection\Service\IpLocation;
use Locale;
use Psr\Http\Message\ServerRequestInterface;
use ResourceBundle;

/**
 * Location Service.
 *
 * Get the Location based on the IP.
 * Use the geoplugin.net API.
 */
class IpLanguage
{
    protected IpLocation $ipLocation;

    public function __construct(IpLocation $ipLocation)
    {
        $this->ipLocation = $ipLocation;
    }

    public function __invoke(DetectUserLanguages $event): void
    {
        $config = $event->getSite()->getConfiguration();
        $addIp = $config['addIpLocationToBrowserLanguage'] ?? '';
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
            default:
                return;
        }
        $event->setUserLanguages($base);
    }

    public function getLanguage(ServerRequestInterface $request): ?string
    {
        $data = $this->ipLocation->get($request->getServerParams()['REMOTE_ADDR'] ?? '');
        if (null !== $data && !isset($data['geoplugin_countryCode'])) {
            return null;
        }

        return $this->mapCountryToLanguage($data['geoplugin_countryCode']);
    }

    protected function mapCountryToLanguage(string $country): string
    {
        $subtags = ResourceBundle::create('likelySubtags', 'ICUDATA', false);
        $dummy = 'und_' . strtoupper($country);
        $locale = $subtags->get($dummy) ?: $subtags->get('und');

        return Locale::getPrimaryLanguage($locale);
    }
}
