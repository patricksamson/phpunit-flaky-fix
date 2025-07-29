<?php

namespace PatrickSamson\PHPUnitFlakyFix\Concerns;

trait ManagesGlobalSeed
{
    protected static $globalSeed;

    public function initializeGlobalSeed(): void
    {
        // If the global seed is already initialized, re-seed the random number generator.
        if ($this->isGlobalSeedInitialized()) {
            $this->seedRandomnessSources();

            return;
        }

        // Check if a specific seed is provided via environment variable.
        if ($this->isSeedProvidedFromEnv()) {
            $this->setGlobalSeed($this->getSeedFromEnv());
            ray(static::$globalSeed)->label('Using Seed from Environment')->blue();

            return;
        }

        // Use a more robust locking mechanism
        $lockFilePath = sys_get_temp_dir() . '/phpunit-global-seed.lock';
        $seedFilePath = sys_get_temp_dir() . '/phpunit-global-seed.txt';
        $lockHandle = fopen($lockFilePath, 'c+');

        if ($lockHandle === false) {
            // Fallback if can't create lock file.
            $this->setGlobalSeed($this->generateRandomSeed());
            ray(static::$globalSeed)->label('Fallback Seed Generation')->red();

            return;
        }

        // Try to get exclusive lock with blocking
        if (flock($lockHandle, LOCK_EX)) {
            // Check if seed file already exists (another process might have created it)
            if (file_exists($seedFilePath)) {
                // Read existing seed
                $this->setGlobalSeed((int) file_get_contents($seedFilePath));
                ray(static::$globalSeed)->label('Read Global Seed from lock file')->green();
            } else {
                // This is the first process, generate and write the seed
                $this->setGlobalSeed($this->generateRandomSeed());
                file_put_contents($seedFilePath, static::$globalSeed);
                ray(static::$globalSeed)->label('Generated Global Seed')->purple();

                // Register cleanup for the first process
                register_shutdown_function([$this, 'cleanupLockFiles']);
            }

            flock($lockHandle, LOCK_UN);
        } else {
            // Fallback if locking fails
            static::$globalSeed = mt_rand(0, mt_getrandmax());
            ray(static::$globalSeed)->label('Fallback Seed Generation')->red();
        }

        fclose($lockHandle);
    }

    public function isGlobalSeedInitialized(): bool
    {
        return static::$globalSeed !== null;
    }

    public function setGlobalSeed(int $seed): void
    {
        static::$globalSeed = $seed;
        $this->seedRandomnessSources();
    }

    public function seedRandomnessSources(): void
    {
        if (static::$globalSeed === null) {
            throw new \RuntimeException('Global seed is not initialized.');
        }

        // Seed the random number generator with the global seed
        mt_srand(static::$globalSeed);
    }

    /**
     * Determines if a specific seed is provided via environment variable.
     */
    public function isSeedProvidedFromEnv(): bool
    {
        return $this->getSeedFromEnv() !== null;
    }

    /**
     * Retrieves the seed from the environment variable
     */
    public function getSeedFromEnv(): ?int
    {
        $envSeed = getenv('TEST_SEED');

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
        $lockFilePath = sys_get_temp_dir() . '/phpunit-global-seed.lock';
        $seedFilePath = sys_get_temp_dir() . '/phpunit-global-seed.txt';

        if (file_exists($lockFilePath)) {
            @unlink($lockFilePath);
        }

        if (file_exists($seedFilePath)) {
            @unlink($seedFilePath);
        }
    }
}
