<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Language Detection',
    'description' => 'Modern language detection middleware for TYPO3. Based on PSR-14 & PSR-15.',
    'category' => 'fe',
    'version' => '1.6.1',
    'state' => 'stable',
    'author' => 'Tim Lochmüller',
    'author_email' => 'tim@fruit-lab.de',
    'author_company' => 'hdnet.de',
    'constraints' => [
        'depends' => [
            'php' => '7.4.0-8.0.99',
            'typo3' => '10.4.6-11.5.99',
            'frontend' => '10.4.6-11.5.99',
        ],
    ],
    'autoload' => [
        'psr-4' => ['LD\\LanguageDetection\\' => 'Classes']
    ],
];
