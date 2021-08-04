<?php

declare(strict_types=1);

use LD\LanguageDetection\Service\TcaLanguageSelection;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

$GLOBALS['SiteConfiguration']['site']['columns']['enableLanguageDetection'] = [
    'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:enable',
    'config' => [
        'type' => 'check',
        'default' => '1',
    ],
];

$GLOBALS['SiteConfiguration']['site']['columns']['addIpLocationToBrowserLanguage'] = [
    'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:ip.location.to.language',
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

$version11Branch = VersionNumberUtility::convertVersionNumberToInteger(TYPO3_branch) >= VersionNumberUtility::convertVersionNumberToInteger('11.2');

if ($version11Branch) {
    $GLOBALS['SiteConfiguration']['site']['columns']['fallbackDetectionLanguage'] = [
        'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:fallback.detection.language',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'itemsProcFunc' => TcaLanguageSelection::class . '->get',
        ],
    ];
} else {
    $GLOBALS['SiteConfiguration']['site']['columns']['fallbackDetectionLanguage'] = [
        'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:fallback.detection.language',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'special' => 'languages',
            'items' => [
                ['', ''],
            ],
            'default' => '',
        ],
    ];
}

$GLOBALS['SiteConfiguration']['site']['columns']['allowAllPaths'] = [
    'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:allow.all.paths',
    'config' => [
        'type' => 'check',
        'default' => '0',
    ],
];

$GLOBALS['SiteConfiguration']['site']['columns']['redirectHttpStatusCode'] = [
    'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:redirect.status.code',
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
    'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:disable.with.backend.session',
    'config' => [
        'type' => 'check',
        'default' => 0,
    ],
];
$GLOBALS['SiteConfiguration']['site']['columns']['forwardRedirectParameters'] = [
    'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:forward.redirect.parameter',
    'config' => [
        'type' => 'input',
        'default' => 'gclid',
    ],
];

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] = str_replace(
    'base,',
    'base,--div--;LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:language.detection,enableLanguageDetection,allowAllPaths,fallbackDetectionLanguage,addIpLocationToBrowserLanguage,redirectHttpStatusCode,disableRedirectWithBackendSession,forwardRedirectParameters,',
    $GLOBALS['SiteConfiguration']['site']['types']['0']['showitem']
);

$GLOBALS['SiteConfiguration']['site_language']['columns']['excludeFromLanguageDetection'] = [
    'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:exclude.language.from.detection',
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
