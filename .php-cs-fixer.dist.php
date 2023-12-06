<?php

$finder = PhpCsFixer\Finder::create()
    ->ignoreDotFiles(true)
    ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config->setFinder($finder)
    ->setRules([
        'assign_null_coalescing_to_coalesce_equal' => true,
        '@PSR12'                       => true,
        'array_syntax'                 => ['syntax' => 'short'],
        'concat_space'                 => ['spacing' => 'one'],
        'single_import_per_statement'  => false,
        'single_blank_line_at_eof'     => true,
        'blank_lines_before_namespace' => true,
        'single_line_after_imports'    => true,
        'no_unused_imports'            => true,
        'group_import'                 => true,
        'global_namespace_import'      => [
            'import_classes'   => true,
            'import_functions' => true,
        ],
        'phpdoc_order' => [
            'order' => ['param', 'throws', 'return']
        ],
        'ordered_imports' => [
            'imports_order'  => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha',
        ],
        'ordered_class_elements' => [
            'order' => ['use_trait']
        ],
    ])
;
