--TEST--
With default configuration of extension
--FILE--
<?php

declare(strict_types=1);

passthru('FLAKY_SEED=1234567890 vendor/bin/paratest --configuration=tests/EndToEnd/ParaTest/Usage/WithEnv/phpunit.xml');
--EXPECTF--
ParaTest %a

Flaky Test Seed: 1234567890. To reproduce, run `FLAKY_SEED=1234567890 php artisan test --filter ...`

Processes: %a
