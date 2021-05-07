<?php

/** @var PhpCsFixer\Config $config */
$config = require __DIR__ . '/.php_cs.dist';

$rulesProvider = new Facile\CodingStandards\Rules\CompositeRulesProvider([
    new Facile\CodingStandards\Rules\DefaultRulesProvider(),
    new Facile\CodingStandards\Rules\RiskyRulesProvider(),
    new Facile\CodingStandards\Rules\ArrayRulesProvider([
        'visibility_required' => ['property', 'method', 'const'],
        'heredoc_indentation' => true,
        'heredoc_to_nowdoc' => true,
        'no_null_property_initialization' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'constant_case' => true,
        'declare_strict_types' => true,
        'indentation_type' => true,
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true,
        ],
        'phpdoc_line_span' => [
            'const' => 'single',
            'method' => 'multi',
            'property' => 'single',
        ],
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        // risky rules
        'fopen_flag_order' => true,
        'fopen_flags' => true,
        'ereg_to_preg' => true,
        'implode_call' => true,
        'no_unset_on_property' => true,
        // custom
        'comment_to_phpdoc' => true,
        'phpdoc_to_comment' => false,
    ]),
]);

$config->setRules($rulesProvider->getRules());
$config->setFinder(
    $config->getFinder()->notName('bootstrap.php')
);
$config->setUsingCache(false);
$config->setRiskyAllowed(false);

return $config;