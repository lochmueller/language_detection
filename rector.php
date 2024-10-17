<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\PostRector\Rector\NameImportingPostRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Ssch\TYPO3Rector\Configuration\Typo3Option;
use Ssch\TYPO3Rector\FileProcessor\Composer\Rector\ExtensionComposerRector;
use Ssch\TYPO3Rector\FileProcessor\TypoScript\Rector\FileIncludeToImportStatementTypoScriptRector;
use Ssch\TYPO3Rector\Rector\v9\v0\InjectAnnotationRector;
use Ssch\TYPO3Rector\Rector\General\ExtEmConfRector;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;
use Ssch\TYPO3Rector\Set\Typo3SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (\Rector\Config\RectorConfig $config): void {

    $config->import(Typo3LevelSetList::UP_TO_TYPO3_13);
    $config->import(SetList::CODE_QUALITY);
    $config->import(SetList::TYPE_DECLARATION);
    $config->import(LevelSetList::UP_TO_PHP_81);

    $config->paths([
        'ext_emconf.php',
        'composer.json',
        __DIR__ . '/Classes/',
        __DIR__ . '/Configuration/',
        __DIR__ . '/Tests/',
    ]);

    $config->skip([
        NameImportingPostRector::class => [
            'ext_emconf.php',
            'ext_localconf.php',
            'ext_tables.php',
        ],
    ]);
};
