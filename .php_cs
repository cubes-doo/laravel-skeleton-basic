<?php

$rules = [
    '@PSR2' => true,
    'array_syntax' => ['syntax' => 'short'],
    'no_multiline_whitespace_before_semicolons' => true,
    'no_short_echo_tag' => true,
    'no_unused_imports' => true,
    'no_empty_comment' => true,
    'not_operator_with_successor_space' => true,
    'no_useless_else' => true,
    'ordered_imports' => [
        'sortAlgorithm' => 'length',
    ],
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_indent' => true,
    'phpdoc_no_package' => true,
    'phpdoc_order' => true,
    'phpdoc_separation' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_trim' => true,
    'phpdoc_var_without_name' => true,
    'phpdoc_to_comment' => true,
    'single_quote' => true,
    'ternary_operator_spaces' => true,
    'trailing_comma_in_multiline_array' => true,
    'trim_array_spaces' => true,

    'no_trailing_comma_in_singleline_array' => false,
    'single_import_per_statement' => false,
    //'header_comment' => [ // for file-level comment deletion
    //    'header' => '',
    //],
    'binary_operator_spaces' => [
        'operators' => [
            '=' => 'single_space',
        ]
    ],
];

return PhpCsFixer\Config::create()
    ->setRules($rules)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/app')
            ->in(__DIR__ . '/database/seeds')
            ->in(__DIR__ . '/database/factories')
            ->in(__DIR__ . '/database/migrations')
            ->in(__DIR__ . '/config')
            ->in(__DIR__ . '/routes')
    );