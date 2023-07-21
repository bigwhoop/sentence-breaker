<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()->in([
    __DIR__ . '/src',
    __DIR__ . '/tests/src',
    __DIR__ . '/data',
]);

$config = new PhpCsFixer\Config();
$config->setFinder($finder);
$config->setRules([
    '@Symfony' => true,
    'concat_space' => [
        'spacing' => 'one',
    ],
    'yoda_style' => false,
]);

return $config;