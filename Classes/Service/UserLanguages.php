<?php

namespace LD\LanguageDetection\Service;

use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Site\Entity\Site;

class UserLanguages
{
    protected BrowserLanguage $browserLanguage;

    protected IpPosition $ipPosition;

    protected Normalizer $normalizer;

    public function __construct(BrowserLanguage $browserLanguage, IpPosition $ipPosition, Normalizer $normalizer)
    {
        $this->browserLanguage = $browserLanguage;
        $this->ipPosition = $ipPosition;
        $this->normalizer = $normalizer;
    }

    public function get(Site $site): array
    {
        $config = $site->getConfiguration();
        $addIp = $config['addIpLocationToBrowserLanguage'] ?? '';
        $browserLanguages = $this->browserLanguage->get();
        if ($addIp) {
            try {
                $data = $this->ipPosition->get();
                if (!isset($data['geoplugin_countryCode']) || $data['geoplugin_countryCode'] === null) {
                    throw new Exception('Not found', 12738);
                }
                $countryCode = 'xx_' . \mb_strtolower($data['geoplugin_countryCode']);
                switch ($addIp) {
                    case 'before':
                        \array_unshift($browserLanguages, $countryCode);
                        break;
                    case 'after':
                        $browserLanguages[] = $countryCode;
                        break;
                    case 'replace':
                        $browserLanguages = [$countryCode];
                        break;
                    default:
                        // there is no default action
                }
            } catch (\Exception $exception) {
            }
        }

        return $this->normalizer->normalizeList($browserLanguages);
    }
}
