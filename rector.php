<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/assets',
        __DIR__.'/config',
        __DIR__.'/public',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withImportNames()
    ->withSets([
        LevelSetList::UP_TO_PHP_83,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::CODING_STYLE,
        SetList::INSTANCEOF,
    ])
    // uncomment to reach your current PHP version
    ->withPhpSets(php83: true)
    ->withRules([
        AddVoidReturnTypeWhereNoReturnRector::class,
        InlineConstructorDefaultToPropertyRector::class,
        ReturnTypeFromStrictNativeCallRector::class,
    ]);
