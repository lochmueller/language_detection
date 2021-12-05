<?php

use Lochmueller\LanguageDetection\Middleware\JsonDetectionMiddleware;
use Lochmueller\LanguageDetection\Middleware\LanguageDetectionMiddleware;

return [
    'frontend' => [
        'language-detection/default-handle' => [
            'target' => LanguageDetectionMiddleware::class,
            'before' => [
                'typo3/cms-frontend/base-redirect-resolver',
            ],
            'after' => [
                'typo3/cms-frontend/site',
            ],
        ],
        'language-detection/json-handle' => [
            'target' => JsonDetectionMiddleware::class,
            'before' => [
                'typo3/cms-frontend/base-redirect-resolver',
            ],
            'after' => [
                'typo3/cms-frontend/site',
            ],
        ],
    ],
];
