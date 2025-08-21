--TEST--
With default configuration of extension
--FILE--
<?php

declare(strict_types=1);

passthru('vendor/bin/pest --parallel --configuration=tests/EndToEnd/Pest4/Usage/Default/phpunit.xml');
--EXPECTF--
%s Flaky Test Seed: %d. To reproduce, run `FLAKY_SEED=%d php artisan test --filter ...`.


%s.%a
