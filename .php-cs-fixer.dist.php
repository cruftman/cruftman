<?php declare(strict_types=1);

$header = <<<'EOF'
This file is part of cruftman/cruftman.

Copyright (c) Paweł Tomulik <pawel@tomulik.pl>

View the LICENSE file for full copyright and license information.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->in(__DIR__ . '/src')
    ->name('*.php')
;

$config = new PhpCsFixer\Config();

return $config
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@PhpCsFixer' => true,
        'blank_line_after_opening_tag' => false,
        'linebreak_after_opening_tag' => false,
        'declare_strict_types' => true,
        'header_comment' => [
            'header' => $header,
            'location' => 'after_declare_strict',
        ],
        'array_syntax' => ['syntax' => 'short'],
        'psr_autoloading' => true,
        'binary_operator_spaces' => [
            'operators' => [
                '=>' => 'align_single_space_minimal',
                '='  => 'single_space'
            ],
        ],
        // 'phpdoc_to_comment' => true, didn't play well with annotations we
        // needed for psalm
        'phpdoc_to_comment' => false,
        'no_superfluous_phpdoc_tags' => false,
    ])
;
// vim: syntax=php sw=4 ts=4 et:
