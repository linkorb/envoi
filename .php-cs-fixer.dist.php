<?php
// Managed by https://github.com/linkorb/repo-ansible. Manual changes will be overwritten.

$finder = PhpCsFixer\Finder::create()
    ->in("src")
;

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@PSR1' => true,
    '@PSR2' => true,
    '@Symfony' => true,
    'blank_line_before_statement' => [
        'statements' => [
            'declare',
            'return',
        ],
    ],
    'global_namespace_import' => [
        'import_classes' => true,
        'import_constants' => true,
        'import_functions' => true,
    ],
    'phpdoc_align' => false,
    'single_line_throw' => false,
])
->setFinder($finder)
->setCacheFile('.php-cs-fixer.cache') // forward compatibility with 3.x line
;
