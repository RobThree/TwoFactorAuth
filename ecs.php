<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Whitespace\TypesSpacesFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/lib',
        __DIR__ . '/tests',
        __DIR__ . '/testsDependency',
    ]);

    $ecsConfig->sets([
        SetList::ARRAY,
        SetList::CLEAN_CODE,
        SetList::CONTROL_STRUCTURES,
        SetList::DOCBLOCK,
        SetList::NAMESPACES,
        SetList::PSR_12,
        SetList::SPACES,
    ]);

    $ecsConfig->dynamicSets([
        '@PHP82Migration',
    ]);

    $ecsConfig->rule(ArraySyntaxFixer::class);
    $ecsConfig->rule(TypesSpacesFixer::class);
};
