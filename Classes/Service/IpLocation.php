<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

class IpLocation
{
    public function get(string $ip): ?array
    {
        try {
            $urlService = 'http://www.geoplugin.net/php.gp?ip=' . $ip;
            $version11Branch = VersionNumberUtility::convertVersionNumberToInteger(GeneralUtility::makeInstance(Typo3Version::class)->getBranch()) >= VersionNumberUtility::convertVersionNumberToInteger('11.2');
            if ($version11Branch) {
                $content = unserialize(GeneralUtility::getUrl($urlService));
            } else {
                $content = unserialize(GeneralUtility::getUrl($urlService, 0, false));
            }
            if (!\is_array($content) || empty($content) || '404' === $content['geoplugin_status']) {
                throw new \Exception('Missing information in response', 123781);
            }

            return $content;
        } catch (\Exception $exc) {
            return null;
        }
    }
}
