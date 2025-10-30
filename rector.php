<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Exception\Configuration\InvalidConfigurationException;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

// Docs: https://getrector.com/
try {
    return RectorConfig::configure()
        ->withPaths([
            __DIR__.'/lib',
//            __DIR__.'/test',
        ])
        // Will grab the PHP version out of the composer.json file so it doesn't need to be explicitly declared.
        ->withPhpSets()
        ->withPreparedSets(
        )
        ->withRules([
            DeclareStrictTypesRector::class,
        ])
        ->withAttributesSets(
            phpunit: true,
        );
} catch (InvalidConfigurationException $e) {
    dd($e);
}
