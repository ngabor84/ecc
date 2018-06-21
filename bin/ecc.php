#!/usr/bin/env php

<?php

if (PHP_SAPI !== 'cli') {
    echo 'Warning: Environment Consistency Checker should be invoked via the CLI version of PHP, not the '.PHP_SAPI.' SAPI'.PHP_EOL;
}

require_once __DIR__ . '/../vendor/autoload.php';

use Ecc\CheckCommand;
use Symfony\Component\Console\Application;

$app = new Application('Environment Consistency Checker', '0.1.0');
$app->add(new CheckCommand());
$app->run();