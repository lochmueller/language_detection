<?php

use TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;

$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['language_detection_ip_location'] ??= [
    'frontend' => VariableFrontend::class,
    'backend' => Typo3DatabaseBackend::class,
    'options' => ['defaultLifetime' => 86400], // 24h
    'groups' => ['system'],
];