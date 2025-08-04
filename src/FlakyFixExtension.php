<?php

declare(strict_types=1);

namespace PatrickSamson\PHPUnitFlakyFix;

use PatrickSamson\PHPUnitFlakyFix\Concerns\ManagesGlobalSeed;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

final class FlakyFixExtension implements Extension
{
    use ManagesGlobalSeed;

    public function bootstrap(
        Configuration $configuration,
        Facade $facade,
        ParameterCollection $parameters
    ): void {
        $this->initializeGlobalSeed();

        $facade->registerSubscriber(new PreparationStartedSubscriber());

        /**
         * Do not respect the `no-output` configuration for this extension.
         * This is necessary to work with `php artisan test`, Pest, and Paratest.
         */

        echo PHP_EOL
            . sprintf('Global Seed: %s. To reproduce, run `FLAKY_SEED=%s php artisan test --filter ...`', self::$globalSeed, self::$globalSeed)
            . PHP_EOL . PHP_EOL;
    }
}
