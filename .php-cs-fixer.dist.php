<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('tests/Extension/Symfony/Fixtures/app/var')
    ->exclude('vendor');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'braces' => [
            'allow_single_line_closure' => true,
        ],
        'declare_strict_types' => true,
        'modernize_types_casting' => true,
        'native_function_invocation' => ['include' => ['@compiler_optimized'], 'scope' => 'namespaced'],
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_imports' => true,
        'phpdoc_add_missing_param_annotation' => [
            'only_untyped' => true,
        ],
        'phpdoc_order' => true,
        'phpdoc_to_comment' => false,
        'semicolon_after_instruction' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'ternary_to_null_coalescing' => true,
    ])
    ->setFinder($finder);
