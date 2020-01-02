<?php
declare(strict_types = 1);

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests/BrowscapTest')
    ->in(__DIR__ . '/tests/UserAgentsTest')
    ->in(__DIR__ . '/tests/fixtures')
    ->append([__FILE__]);

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP70Migration' => true,
        '@PHP70Migration:risky' => true,
        '@PHP71Migration' => true,
        '@PHP71Migration:risky' => true,
        '@PHP73Migration' => true,
        '@PHPUnit60Migration:risky' => true,
        '@PHPUnit75Migration:risky' => true,

        // @PSR2 rules configured different from default
        'blank_line_after_namespace' => true,
        'class_definition' => [
            'single_line' => false,
            'single_item_single_line' => true,
            'multi_line_extends_each_single_line' => true,
        ],
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => false,
        ],
        'no_break_comment' => false,
        'visibility_required' => ['elements' => ['property', 'method', 'const']],

        // @PhpCsFixer rules configured different from default
        'align_multiline_comment' => ['comment_type' => 'all_multiline'],
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'single_space', 'operators' => ['=' => 'align_single_space_minimal']],
        'php_unit_internal_class' => false,
        'concat_space' => ['spacing' => 'one'],
        'declare_equal_normalize' => ['space' => 'single'],
        'php_unit_internal_class' => false,
        'no_superfluous_phpdoc_tags' => false,
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
        'no_extra_blank_lines' => [
            'tokens' => ['break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use', 'useTrait', 'use_trait'],
        ],
        'no_useless_return' => false,
        'php_unit_test_class_requires_covers' => false,
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
        'phpdoc_no_empty_return' => false,
        'phpdoc_summary' => false,
        'single_blank_line_before_namespace' => false,
        'single_line_comment_style' => ['comment_types' => ['hash']],
        'blank_line_after_opening_tag' => false,
        'return_type_declaration' => ['space_before' => 'one'],

        // @PhpCsFixer:risky rules configured different from default
        'php_unit_strict' => ['assertions' => ['assertAttributeEquals', 'assertAttributeNotEquals', 'assertNotEquals']],
        'no_alias_functions' => ['sets' => ['@internal', '@IMAP', '@mbreg', '@all']],
        'php_unit_test_case_static_method_calls' => ['call_type' => 'static'],
        'strict_param' => false,

        // @Symfony rules configured different from default
        'no_blank_lines_after_phpdoc' => false,
        'space_after_semicolon' => ['remove_in_empty_for_expressions' => true],
        'yoda_style' => [
            'equal' => true,
            'identical' => true,
            'less_and_greater' => true,
        ],
        'single_line_throw' => false,

        // @Symfony:risky rules configured different from default
        'non_printable_character' => ['use_escape_sequences_in_strings' => true],

        // @PHP70Migration rules configured different from default
        'ternary_to_null_coalescing' => false,

        // @PHP70Migration:risky rules configured different from default
        'pow_to_exponentiation' => false,

        // @PHPUnit60Migration:risky rules configured different from default
        'php_unit_dedicate_assert' => ['target' => 'newest'],

        // other rules
        'backtick_to_shell_exec' => true,
        'class_keyword_remove' => false,
        'final_class' => false,
        'final_internal_class' => [
            'annotation-black-list' => ['@final', '@Entity', '@ORM'],
            'annotation-white-list' => ['@internal'],
        ],
        'final_public_method_for_abstract_class' => true,
        'final_static_access' => true,
        'general_phpdoc_annotation_remove' => [
            'expectedExceptionMessageRegExp',
            'expectedException',
            'expectedExceptionMessage',
        ],
        'global_namespace_import' => false,
        'header_comment' => false,
        'linebreak_after_opening_tag' => true,
        'list_syntax' => ['syntax' => 'short'],
        'mb_str_functions' => true,
        'native_constant_invocation' => false,
        'native_function_invocation' => false,
        'no_blank_lines_before_namespace' => true,
        'no_null_property_initialization' => true,
        'no_php4_constructor' => true,
        'not_operator_with_space' => false,
        'not_operator_with_successor_space' => false,
        'nullable_type_declaration_for_default_null_value' => ['use_nullable_type_declaration' => true],
        'ordered_class_elements' => false,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'ordered_interfaces' => ['direction' => 'ascend', 'order' => 'alpha'],
        'php_unit_size_class' => false,
        'php_unit_test_annotation' => ['case' => 'camel', 'style' => 'prefix'],
        'phpdoc_to_param_type' => false,
        'phpdoc_to_return_type' => false,
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'alpha',
        ],
        'psr0' => true,
        'self_static_accessor' => true,
        'simplified_null_return' => false,
        'static_lambda' => true,
    ])
    ->setUsingCache(true)
    ->setFinder($finder);
