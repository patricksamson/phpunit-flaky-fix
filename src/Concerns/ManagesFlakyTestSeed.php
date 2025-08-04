<?php

namespace PatrickSamson\PHPUnitFlakyFix\Concerns;

trait ManagesFlakyTestSeed
{
    protected static $flakySeed;

    public function initializeFlakySeed(): void
    {
        // If the global seed is already initialized, re-seed the random number generator.
        if ($this->isFlakySeedInitialized()) {
            $this->seedRandomnessSources();

            return;
        }

        // Check if a specific seed is provided via environment variable.
        if ($this->isSeedProvidedFromEnv()) {
            $this->setFlakyTestSeed($this->getFlakySeedFromEnv());
            //ray(static::$globalSeed)->label('Using Seed from Environment')->blue();

            return;
        }

        // Use a more robust locking mechanism
        $lockFilePath = static::getLockFilePath();
        $seedFilePath = static::getSeedFilePath();
        $lockHandle = fopen($lockFilePath, 'c+');

        if ($lockHandle === false) {
            // Fallback if can't create lock file.
            $this->setFlakyTestSeed($this->generateRandomSeed());
            //ray(static::$globalSeed)->label('Fallback Seed Generation')->red();

            return;
        }

        // Try to get exclusive lock with blocking
        if (flock($lockHandle, LOCK_EX)) {
            // Check if seed file already exists (another process might have created it)
            if (file_exists($seedFilePath)) {
                // Read existing seed
                $this->setFlakyTestSeed((int) file_get_contents($seedFilePath));
                //ray(static::$globalSeed)->label('Read Global Seed from lock file')->green();
            } else {
                // This is the first process, generate and write the seed
                $this->setFlakyTestSeed($this->generateRandomSeed());
                file_put_contents($seedFilePath, static::$flakySeed);
                //ray(static::$globalSeed)->label('Generated Global Seed')->purple();

                // Register cleanup for the first process
                register_shutdown_function([$this, 'cleanupLockFiles']);
            }

            flock($lockHandle, LOCK_UN);
        } else {
            // Fallback if locking fails
            static::$flakySeed = mt_rand(0, mt_getrandmax());
            //ray(static::$globalSeed)->label('Fallback Seed Generation')->red();
        }

        fclose($lockHandle);
    }

    public function isFlakySeedInitialized(): bool
    {
        return static::$flakySeed !== null;
    }

    public function setFlakyTestSeed(int $seed): void
    {
        static::$flakySeed = $seed;
        $this->seedRandomnessSources();
    }

    public function seedRandomnessSources(): void
    {
        if (static::$flakySeed === null) {
            throw new \RuntimeException('Global seed is not initialized.');
        }

        // Seed the random number generator with the global seed
        mt_srand(static::$flakySeed);
    }

    /**
     * Determines if a specific seed is provided via environment variable.
     */
    public function isSeedProvidedFromEnv(): bool
    {
        return $this->getFlakySeedFromEnv() !== null;
    }

    /**
     * Retrieves the seed from the environment variable
     */
    public function getFlakySeedFromEnv(): ?int
    {
        $envSeed = getenv('FLAKY_SEED');

        return $envSeed !== false ? (int) $envSeed : null;
    }

    /**
     * Generates a random seed, which will be used to seed the random number generator.
     */
    public function generateRandomSeed(): int
    {
        return mt_rand(0, mt_getrandmax());
    }

    public function cleanupLockFiles(): void
    {
        $lockFilePath = static::getLockFilePath();
        $seedFilePath = static::getSeedFilePath();

        if (file_exists($lockFilePath)) {
            @unlink($lockFilePath);
        }

        if (file_exists($seedFilePath)) {
            @unlink($seedFilePath);
        }
    }

    /**
     * Get the full lock file path
     */
    public static function getLockFilePath(): string
    {
        return sys_get_temp_dir() . '/' . 'phpunit-flaky-seed.lock';
    }

    /**
     * Get the full seed file path
     */
    public static function getSeedFilePath(): string
    {
        return sys_get_temp_dir() . '/' . 'phpunit-flaky-seed.txt';
    }
}
