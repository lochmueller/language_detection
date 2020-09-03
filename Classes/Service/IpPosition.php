<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Location Service.
 *
 * Get the Location based on the IP.
 * Use the geoplugin.net API.
 */
class IpPosition
{
    public function get(?string $ip = null): array
    {
        $ip = '212.100.44.250';
        if (null === $ip) {
            $ip = GeneralUtility::getIndpEnv('REMOTE_ADDR');
        }
        $report = [];
        try {
            $urlService = 'http://www.geoplugin.net/php.gp?ip=' . $ip;
            $content = unserialize(GeneralUtility::getUrl($urlService, 0, false, $report));
        } catch (\Exception $exc) {
            throw new \Exception('Can\'t get the location: ' . var_export($report, true), 561786287945235);
        }

        if (!\count($content)) {
            throw new \Exception('No content for the location: ' . var_export($report, true), 23847628734324);
        }

        if ('404' === $content['geoplugin_status']) {
            throw new \Exception('IP location not found: ' . var_export($report, true), 561786287945237);
        }

        return $content;
    }

    public function getLanguage(?string $ip = null): ?string
    {
        $data = $this->get($ip);
        if (isset($data['geoplugin_countryCode'])) {
            return (string)$this->mapCountryToLanguage($data['geoplugin_countryCode']);
        }
    }

    public function mapCountryToLanguage(string $country): string
    {
        $subtags = \ResourceBundle::create('likelySubtags', 'ICUDATA', false);
        $country = \Locale::canonicalize('und_' . $country);
        $locale = $subtags->get($country) ?: $subtags->get('und');

        return \Locale::getPrimaryLanguage($locale);
    }
}