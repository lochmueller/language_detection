<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Language Detection',
    'description' => 'Modern language detection middleware for TYPO3. Based on PSR-7, PSR-14 & PSR-15.',
    'category' => 'fe',
    'version' => '4.0.0',
    'state' => 'stable',
    'author' => 'Tim Lochmüller',
    'author_email' => 'tim@fruit-lab.de',
    'author_company' => 'hdnet.de',
    'constraints' => [
        'depends' => [
            'php' => '8.0.0-8.1.99',
            'typo3' => '11.5.0-12.99.99'
        ],
    ],
    'autoload' => [
        'psr-4' => ['Lochmueller\\LanguageDetection\\' => 'Classes']
    ],
];
