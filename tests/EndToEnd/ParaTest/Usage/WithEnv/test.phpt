--TEST--
With default configuration of extension
--FILE--
<?php

declare(strict_types=1);

passthru('TEST_SEED=1234567890 vendor/bin/paratest --configuration=tests/EndToEnd/ParaTest/Usage/Default/phpunit.xml');
--EXPECTF--
ParaTest %a

Global Seed: 1234567890. To reproduce, run `TEST_SEED=1234567890 php artisan test --filter ...`

%a
