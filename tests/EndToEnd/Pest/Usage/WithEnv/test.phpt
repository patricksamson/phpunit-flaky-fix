--TEST--
With default configuration of extension
--FILE--
<?php

declare(strict_types=1);

passthru('FLAKY_SEED=1234567890 vendor/bin/pest --configuration=tests/EndToEnd/Pest/Usage/Default/phpunit.xml');
--EXPECTF--
%s Global Seed: 1234567890. To reproduce, run `FLAKY_SEED=1234567890 php artisan test --filter ...`.

%a
