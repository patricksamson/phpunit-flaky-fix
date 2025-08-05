<?php

declare(strict_types=1);

namespace PatrickSamson\PHPUnitFlakyFix\Pest;

use PatrickSamson\PHPUnitFlakyFix\Concerns\ManagesFlakyTestSeed;
use Pest\Contracts\Plugins\Bootable;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class Plugin implements Bootable
{
    use ManagesFlakyTestSeed;

    /**
     * Creates a new Plugin instance.
     */
    public function __construct(private readonly OutputInterface $output)
    {
    }

    public function boot(): void
    {
        $this->initializeFlakySeed();
        if (!$this->isSeedProvidedFromEnv()) {
            putenv('FLAKY_SEED=' . self::$flakySeed);
        }

        $message = sprintf(
            'Flaky Test Seed: %s. To reproduce, run `FLAKY_SEED=%s php artisan test --filter ...`.',
            self::$flakySeed,
            self::$flakySeed
        );
        $this->output->writeln([
            '',
            '  <fg=white;options=bold;bg=blue> INFO </> ' . $message,
            '',
        ]);
    }
}
