<?php

require_once __DIR__ . '/vendor/autoload.php';

use Crada\Apidoc\Builder;

$classes = [
    'App\Controllers\TagControllers\TagController',
];

$outputDir = __DIR__ . '/api-docs';

$builder = new Builder($classes, $outputDir, 'Task Manager API Documentation');
$builder->generate();