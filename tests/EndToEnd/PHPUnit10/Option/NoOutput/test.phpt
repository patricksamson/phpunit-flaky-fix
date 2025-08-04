--TEST--
With default configuration of extension
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

$_SERVER['argv'][] = '--configuration=tests/EndToEnd/PHPUnit10/Option/NoOutput/phpunit.xml';
$_SERVER['argv'][] = '--no-output';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
--EXPECTF--
Flaky Test Seed: %d. To reproduce, run `FLAKY_SEED=%d php artisan test --filter ...`
