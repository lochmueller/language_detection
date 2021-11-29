<?php

use Lochmueller\LanguageDetection\Middleware\LanguageDetection;

return [
    'frontend' => [
        'language-detection/handle' => [
            'target' => LanguageDetection::class,
            'before' => [
                'typo3/cms-frontend/base-redirect-resolver',
            ],
            'after' => [
                'typo3/cms-frontend/site',
            ],
        ],
    ],
];
