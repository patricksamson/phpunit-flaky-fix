--TEST--
With default configuration of extension
--FILE--
<?php

declare(strict_types=1);

passthru('vendor/bin/paratest --configuration=tests/EndToEnd/ParaTest/Usage/Default/phpunit.xml');
--EXPECTF--
ParaTest %a

Global Seed: %d. To reproduce, run `TEST_SEED=%d php artisan test --filter ...`

%a
