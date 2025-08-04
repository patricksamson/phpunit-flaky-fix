<?php

declare(strict_types=1);

namespace PatrickSamson\PHPUnitFlakyFix;

use PatrickSamson\PHPUnitFlakyFix\Concerns\ManagesFlakyTestSeed;
use PHPUnit\Event\Test\PreparationStarted;
use PHPUnit\Event\Test\PreparationStartedSubscriber as TestPreparationStartedSubscriber;

final class PreparationStartedSubscriber implements TestPreparationStartedSubscriber
{
    use ManagesFlakyTestSeed;

    public function __construct()
    {
    }

    public function notify(PreparationStarted $event): void
    {
        $this->initializeFlakySeed();
        //ray(self::$globalSeed)->blue();
    }
}
