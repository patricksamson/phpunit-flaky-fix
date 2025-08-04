--TEST--
With default configuration of extension
--FILE--
<?php

declare(strict_types=1);

passthru('vendor/bin/pest --configuration=tests/EndToEnd/Pest/Usage/Default/phpunit.xml');
--EXPECTF--
%s Global Seed: %d. To reproduce, run `FLAKY_SEED=%d php artisan test --filter ...`.

%a
