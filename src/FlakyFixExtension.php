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

        echo PHP_EOL
            . sprintf('Global Seed: %s. To reproduce, run `TEST_SEED=%s php artisan test --filter ...`', self::$globalSeed, self::$globalSeed)
            . PHP_EOL . PHP_EOL;
    }
}
