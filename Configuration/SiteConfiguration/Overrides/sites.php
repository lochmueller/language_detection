<?php

declare(strict_types=1);

use Lochmueller\LanguageDetection\Service\LocaleCollectionSortService;
use Lochmueller\LanguageDetection\Service\TcaLanguageSelection;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

$GLOBALS['SiteConfiguration']['site']['columns']['enableLanguageDetection'] = [
    'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:enable',
    'config' => [
        'type' => 'check',
        'default' => '1',
    ],
];

$GLOBALS['SiteConfiguration']['site']['palettes']['languageDetectionMaxMind'] = [
    'showitem' => 'languageDetectionMaxMindDatabasePath, --linebreak--, languageDetectionMaxMindAccountId, languageDetectionMaxMindLicenseKey, languageDetectionMaxMindMode',
];

$GLOBALS['SiteConfiguration']['site']['columns']['languageDetectionMaxMindDatabasePath'] = [
    'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:languageDetectionMaxMindDatabasePath',
    'config' => [
        'type' => 'input',
    ],
];

$GLOBALS['SiteConfiguration']['site']['columns']['languageDetectionMaxMindAccountId'] = [
    'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:languageDetectionMaxMindAccountId',
    'config' => [
        'type' => 'input',
    ],
];

$GLOBALS['SiteConfiguration']['site']['columns']['languageDetectionMaxMindLicenseKey'] = [
    'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:languageDetectionMaxMindLicenseKey',
    'config' => [
        'type' => 'input',
    ],
];

$GLOBALS['SiteConfiguration']['site']['columns']['languageDetectionMaxMindMode'] = [
    'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:languageDetectionMaxMindMode',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'items' => [
            ['Before', LocaleCollectionSortService::SORT_BEFORE],
            ['After', LocaleCollectionSortService::SORT_AFTER],
            ['Replace', LocaleCollectionSortService::SORT_REPLACE],
        ],
    ],
];

$GLOBALS['SiteConfiguration']['site']['columns']['addIpLocationToBrowserLanguage'] = [
    'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:ip.location.to.language',
    'config' => [
        'type' => 'select',
        'renderType' => 'selectSingle',
        'items' => [
            ['No', ''],
            ['Before', LocaleCollectionSortService::SORT_BEFORE],
            ['After', LocaleCollectionSortService::SORT_AFTER],
            ['Replace', LocaleCollectionSortService::SORT_REPLACE],
        ],
    ],
];

$version11Branch = VersionNumberUtility::convertVersionNumberToInteger(GeneralUtility::makeInstance(Typo3Version::class)->getBranch()) >= VersionNumberUtility::convertVersionNumberToInteger('11.2');

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

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .=
    ',--div--;LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:language.detection,
        enableLanguageDetection,
        allowAllPaths,
        fallbackDetectionLanguage,
        addIpLocationToBrowserLanguage,
        redirectHttpStatusCode,
        disableRedirectWithBackendSession,
        forwardRedirectParameters,
        --palette--;MaxMind;languageDetectionMaxMind,
    ';

$GLOBALS['SiteConfiguration']['site_language']['columns']['excludeFromLanguageDetection'] = [
    'label' => 'LLL:EXT:language_detection/Resources/Private/Language/locallang.xlf:exclude.language.from.detection',
    'config' => [
        'type' => 'check',
        'default' => '0',
    ],
];

$GLOBALS['SiteConfiguration']['site_language']['types']['1']['showitem'] .= ', excludeFromLanguageDetection, ';
