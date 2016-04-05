<?php
ini_set('memory_limit', '-1');

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->files()
    ->name('*.php')
    //->in(__DIR__ . '/src')
    //->in(__DIR__ . '/tests')
    ->in(__DIR__ . '/tests/fixtures')
;

return Symfony\CS\Config\Config::create()
    ->level(\Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers(
        array(
            'unalign_double_arrow',
            'align_equals',
            'braces',
            'concat_with_spaces',
            'duplicate_semicolon',
            'elseif',
            'empty_return',
            'encoding',
            'eof_ending',
            'extra_empty_lines',
            'function_call_space',
            'function_declaration',
            'indentation',
            'join_function',
            'line_after_namespace',
            'linefeed',
            'list_commas',
            'lowercase_constants',
            'lowercase_keywords',
            'method_argument_space',
            'multiple_use',
            'namespace_no_leading_whitespace',
            'no_blank_lines_after_class_opening',
            'parenthesis',
            'php_closing_tag',
            'phpdoc_indent',
            'phpdoc_no_access',
            'phpdoc_no_empty_return',
            'phpdoc_no_package',
            'phpdoc_params',
            'phpdoc_scalar',
            'phpdoc_separation',
            'phpdoc_to_comment',
            'phpdoc_trim',
            'phpdoc_types',
            'phpdoc_var_without_name',
            'remove_lines_between_uses',
            'return',
            'self_accessor',
            'short_array_syntax',
            'short_tag',
            'single_line_after_imports',
            'single_quote',
            'spaces_before_semicolon',
            'spaces_cast',
            'ternary_spaces',
            'trailing_spaces',
            'trim_array_spaces',
            'unused_use',
            'visibility',
            'whitespacy_lines',
            'psr0',
            'array_element_no_space_before_comma',
            'array_element_white_space_after_comma',
            'blankline_after_open_tag',
            'function_typehint_space',
            'include',
            'multiline_array_trailing_comma',
            'new_with_braces',
            'object_operator',
            'operators_spaces',
            'phpdoc_inline_tag',
            'pre_increment',
            'print_to_echo',
            'remove_leading_slash_use',
            'short_bool_cast',
            'single_array_no_trailing_comma',
            'single_blank_line_before_namespace',
            'standardize_not_equal',
            'ereg_to_preg',
            'multiline_spaces_before_semicolon',
            'newline_after_open_tag',
            'ordered_use',
            'phpdoc_order',
            'short_echo_tag',
            'strict',
        )
    )
    ->finder($finder);

