<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Language Detection',
    'description' => 'Modern language detection middleware for TYPO3. Based on PSR-7, PSR-14 & PSR-15.',
    'category' => 'fe',
    'version' => '5.1.0',
    'state' => 'stable',
    'author' => 'Tim Lochmüller',
    'author_email' => 'tim@fruit-lab.de',
    'author_company' => 'hdnet.de',
    'constraints' => [
        'depends' => [
            'php' => '8.2.0-8.5.99',
            'typo3' => '13.4.0-14.3.99'
        ],
    ],
    'autoload' => [
        'psr-4' => ['Lochmueller\\LanguageDetection\\' => 'Classes']
    ],
];
