<?php

declare(strict_types=1);

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

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] = str_replace(
    'base,',
    'base,--div--;Language Detection,enableLanguageDetection,addIpLocationToBrowserLanguage,',
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

// @todo more detail configuration
