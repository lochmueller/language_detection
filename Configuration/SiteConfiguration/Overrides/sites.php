<?php

$GLOBALS['SiteConfiguration']['site']['columns']['enableLanguageDetection'] = [
    'label' => 'Enable Language Detection',
    'config' => [
        'type' => 'check',
    ],
];

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] = str_replace(
    'base,',
    'base, enableLanguageDetection, ',
    $GLOBALS['SiteConfiguration']['site']['types']['0']['showitem']
);

// @todo more detail configuration
