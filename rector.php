<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;
use Rector\Set\ValueObject\SetList;
use RectorLaravel\Set\LaravelSetList;
use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\If_\SimplifyIfReturnBoolRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector;
use RectorLaravel\Rector\Class_\AddExtendsAnnotationToModelFactoriesRector;
use RectorLaravel\Rector\ClassMethod\AddGenericReturnTypeToRelationsRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector;
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ]);

    $rectorConfig->skip([
        CompactToVariablesRector::class,
        __DIR__.'/resources/views/vendor',
    ]);

    $rectorConfig->cacheDirectory(__DIR__.'/storage/rector');
    $rectorConfig->cacheClass(FileCacheStorage::class);

    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::TYPE_DECLARATION,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_84);

    $rectorConfig->parallel();

    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);

    $rectorConfig->fileExtensions(['php']);

    $rectorConfig->rules([
        ClosureToArrowFunctionRector::class,
        SimplifyIfReturnBoolRector::class,
        AddReturnTypeDeclarationRector::class,
        InlineConstructorDefaultToPropertyRector::class,
        AddGenericReturnTypeToRelationsRector::class,
        AddExtendsAnnotationToModelFactoriesRector::class,
    ]);
};
