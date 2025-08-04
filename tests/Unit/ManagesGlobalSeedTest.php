<?php

declare(strict_types=1);

namespace PatrickSamson\PHPUnitFlakyFix\Tests\Unit;

use PatrickSamson\PHPUnitFlakyFix\Concerns\ManagesGlobalSeed;
use PHPUnit\Framework\TestCase;

final class ManagesGlobalSeedTest extends TestCase
{
    private TestClassUsingManagesGlobalSeed $testInstance;
    private string $lockFilePath;
    private string $seedFilePath;

    protected function setUp(): void
    {
        $this->testInstance = new TestClassUsingManagesGlobalSeed();
        $this->lockFilePath = sys_get_temp_dir() . '/phpunit-global-seed.lock';
        $this->seedFilePath = sys_get_temp_dir() . '/phpunit-global-seed.txt';

        // Clean up any existing files
        $this->cleanupFiles();

        // Reset the static seed property
        $this->testInstance->resetGlobalSeed();
    }

    protected function tearDown(): void
    {
        $this->cleanupFiles();
        $this->testInstance->resetGlobalSeed();

        // Clear environment variable
        putenv('FLAKY_SEED');
    }

    private function cleanupFiles(): void
    {
        if (file_exists($this->lockFilePath)) {
            @unlink($this->lockFilePath);
        }
        if (file_exists($this->seedFilePath)) {
            @unlink($this->seedFilePath);
        }
    }

    public function test_generates_random_seed(): void
    {
        $seed1 = $this->testInstance->generateRandomSeed();
        $seed2 = $this->testInstance->generateRandomSeed();

        $this->assertIsInt($seed1);
        $this->assertIsInt($seed2);
        $this->assertGreaterThanOrEqual(0, $seed1);
        $this->assertGreaterThanOrEqual(0, $seed2);
        $this->assertLessThanOrEqual(mt_getrandmax(), $seed1);
        $this->assertLessThanOrEqual(mt_getrandmax(), $seed2);
    }

    public function test_detects_seed_from_environment(): void
    {
        // Test when no environment variable is set
        $this->assertFalse($this->testInstance->isSeedProvidedFromEnv());
        $this->assertNull($this->testInstance->getSeedFromEnv());

        // Test when environment variable is set
        putenv('FLAKY_SEED=12345');
        $this->assertTrue($this->testInstance->isSeedProvidedFromEnv());
        $this->assertSame(12345, $this->testInstance->getSeedFromEnv());
    }

    public function test_sets_and_checks_global_seed(): void
    {
        $this->assertFalse($this->testInstance->isGlobalSeedInitialized());

        $seed = 54321;
        $this->testInstance->setGlobalSeed($seed);

        $this->assertTrue($this->testInstance->isGlobalSeedInitialized());
        $this->assertSame($seed, $this->testInstance->getGlobalSeed());
    }

    public function FLAKY_SEEDs_random_number_generator(): void
    {
        $seed = 98765;
        $this->testInstance->setGlobalSeed($seed);

        // Get a few random numbers
        $random1 = mt_rand();
        $random2 = mt_rand();
        $random3 = mt_rand();

        // Reset the seed and verify we get the same sequence
        $this->testInstance->seedRandomnessSources();
        $this->assertSame($random1, mt_rand());
        $this->assertSame($random2, mt_rand());
        $this->assertSame($random3, mt_rand());
    }

    public function test_initialize_with_environment_seed(): void
    {
        putenv('FLAKY_SEED=42');

        $this->testInstance->initializeGlobalSeed();

        $this->assertTrue($this->testInstance->isGlobalSeedInitialized());
        $this->assertSame(42, $this->testInstance->getGlobalSeed());
    }

    public function test_initialize_generates_seed_when_no_env_and_no_existing_seed(): void
    {
        $this->testInstance->initializeGlobalSeed();

        $this->assertTrue($this->testInstance->isGlobalSeedInitialized());
        $this->assertIsInt($this->testInstance->getGlobalSeed());
        $this->assertGreaterThanOrEqual(0, $this->testInstance->getGlobalSeed());
    }

    public function test_initialize_reuses_existing_seed(): void
    {
        $originalSeed = 777;
        $this->testInstance->setGlobalSeed($originalSeed);

        // Get some random numbers to verify the state
        $random1 = mt_rand();
        $random2 = mt_rand();

        // Initialize again - should re-seed but keep the same global seed
        $this->testInstance->initializeGlobalSeed();

        $this->assertSame($originalSeed, $this->testInstance->getGlobalSeed());

        // Should get the same sequence again since we re-seeded
        $this->assertSame($random1, mt_rand());
        $this->assertSame($random2, mt_rand());
    }

    public function test_cleanup_lock_files(): void
    {
        // Create some test files
        file_put_contents($this->lockFilePath, 'test');
        file_put_contents($this->seedFilePath, '12345');

        $this->assertFileExists($this->lockFilePath);
        $this->assertFileExists($this->seedFilePath);

        $this->testInstance->cleanupLockFiles();

        $this->assertFileDoesNotExist($this->lockFilePath);
        $this->assertFileDoesNotExist($this->seedFilePath);
    }

    public function FLAKY_SEED_randomness_sources_throws_exception_when_seed_not_initialized(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Global seed is not initialized.');

        $this->testInstance->seedRandomnessSources();
    }
}

// Test class that uses the trait for testing purposes
class TestClassUsingManagesGlobalSeed
{
    use ManagesGlobalSeed;

    public function getGlobalSeed(): ?int
    {
        return static::$globalSeed;
    }

    public function resetGlobalSeed(): void
    {
        static::$globalSeed = null;
    }
}
