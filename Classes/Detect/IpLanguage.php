<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Detect;

use Exception;
use LD\LanguageDetection\Event\DetectUserLanguages;
use Locale;
use Psr\Http\Message\ServerRequestInterface;
use ResourceBundle;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Location Service.
 *
 * Get the Location based on the IP.
 * Use the geoplugin.net API.
 */
class IpLanguage
{
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
        }
        $event->setUserLanguages($base);
    }

    public function get(ServerRequestInterface $request): ?array
    {
        $params = $request->getServerParams();
        $ip = $params['REMOTE_ADDR'];
        try {
            $urlService = 'http://www.geoplugin.net/php.gp?ip=' . $ip;
            $version11Branch = VersionNumberUtility::convertVersionNumberToInteger(TYPO3_branch) >= VersionNumberUtility::convertVersionNumberToInteger('11.2');
            if ($version11Branch) {
                $content = unserialize(GeneralUtility::getUrl($urlService));
            } else {
                $content = unserialize(GeneralUtility::getUrl($urlService, 0, false));
            }
            if (!\is_array($content) || empty($content) || '404' === $content['geoplugin_status']) {
                throw new Exception('Missing information in response', 123781);
            }

            return $content;
        } catch (Exception $exc) {
            return null;
        }
    }

    public function getLanguage(ServerRequestInterface $request): ?string
    {
        $data = $this->get($request);
        if (null !== $data && !isset($data['geoplugin_countryCode'])) {
            return null;
        }

        return (string)$this->mapCountryToLanguage($data['geoplugin_countryCode']);
    }

    protected function mapCountryToLanguage(string $country): string
    {
        $subtags = ResourceBundle::create('likelySubtags', 'ICUDATA', false);
        $dummy = 'und_' . strtoupper($country);
        $locale = $subtags->get($dummy) ?: $subtags->get('und');

        return Locale::getPrimaryLanguage($locale);
    }
}
