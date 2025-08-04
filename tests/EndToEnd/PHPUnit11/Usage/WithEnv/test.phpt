--TEST--
With default configuration of extension
--FILE--
<?php

declare(strict_types=1);

use PHPUnit\TextUI;

putenv('FLAKY_SEED=1234567890');
$_SERVER['argv'][] = '--configuration=tests/EndToEnd/PHPUnit11/Usage/WithEnv/phpunit.xml';

require_once __DIR__ . '/../../../../../vendor/autoload.php';

$application = new TextUI\Application();

$application->run($_SERVER['argv']);
putenv('FLAKY_SEED');
--EXPECTF--
Global Seed: 1234567890. To reproduce, run `FLAKY_SEED=1234567890 php artisan test --filter ...`

%a
