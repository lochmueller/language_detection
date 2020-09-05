<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\HttpUtility;

$GLOBALS['SiteConfiguration']['site']['columns']['enableLanguageDetection'] = [
    'label' => 'Enable',
    'config' => [
        'type' => 'check',
        'default' => '1',
    ],
];

$GLOBALS['SiteConfiguration']['site']['columns']['addIpLocationToBrowserLanguage'] = [
    'label' => 'Add IP location to browser language',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'items' => [
            ['No', ''],
            ['Before', 'before'],
            ['After', 'after'],
            ['Replace', 'replace'],
        ],
    ],
];

$GLOBALS['SiteConfiguration']['site']['columns']['redirectHttpStatusCode'] = [
    'label' => 'Redirect HTTP status code',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'items' => [
            [HttpUtility::HTTP_STATUS_307, 307],
            [HttpUtility::HTTP_STATUS_302, 302],
            [HttpUtility::HTTP_STATUS_303, 303],
            [HttpUtility::HTTP_STATUS_300, 300],
        ],
    ],
];

$GLOBALS['SiteConfiguration']['site']['columns']['disableRedirectWithBackendSession'] = [
    'label' => 'Disable redirect with backend session',
    'config' => [
        'type' => 'check',
        'default' => 0,
    ],
];
$GLOBALS['SiteConfiguration']['site']['columns']['forwardRedirectParameters'] = [
    'label' => 'Forward redirect parameters',
    'config' => [
        'type' => 'input',
        'default' => 'gclid',
    ],
];

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] = str_replace(
    'base,',
    'base,--div--;Language Detection,enableLanguageDetection,addIpLocationToBrowserLanguage,redirectHttpStatusCode,disableRedirectWithBackendSession,forwardRedirectParameters,',
    $GLOBALS['SiteConfiguration']['site']['types']['0']['showitem']
);

$GLOBALS['SiteConfiguration']['site_language']['columns']['excludeFromLanguageDetection'] = [
    'label' => 'Exclude From Language Detection',
    'config' => [
        'type' => 'check',
        'default' => '0',
    ],
];

$GLOBALS['SiteConfiguration']['site_language']['types']['1']['showitem'] = str_replace(
    'flag,',
    'flag, excludeFromLanguageDetection, ',
    $GLOBALS['SiteConfiguration']['site_language']['types']['1']['showitem']
);
