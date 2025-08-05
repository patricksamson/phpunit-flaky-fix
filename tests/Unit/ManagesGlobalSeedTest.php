<?php

declare(strict_types=1);

namespace PatrickSamson\PHPUnitFlakyFix\Tests\Unit;

use PatrickSamson\PHPUnitFlakyFix\Concerns\ManagesFlakyTestSeed;
use PHPUnit\Framework\TestCase;

final class ManagesGlobalSeedTest extends TestCase
{
    private TestClassUsingManagesGlobalSeed $testInstance;

    protected function setUp(): void
    {
        $this->testInstance = new TestClassUsingManagesGlobalSeed();

        // Clean up any existing files
        $this->testInstance->cleanupLockFiles();

        // Reset the static seed property
        $this->testInstance->resetGlobalSeed();
    }

    protected function tearDown(): void
    {
        $this->testInstance->cleanupLockFiles();
        $this->testInstance->resetGlobalSeed();

        // Clear environment variable
        putenv('FLAKY_SEED');
    }

    public function testGeneratesRandomSeed(): void
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

    public function testDetectsSeedFromEnvironment(): void
    {
        // Test when no environment variable is set
        $this->assertFalse($this->testInstance->isSeedProvidedFromEnv());
        $this->assertNull($this->testInstance->getFlakySeedFromEnv());

        // Test when environment variable is set
        putenv('FLAKY_SEED=12345');
        $this->assertTrue($this->testInstance->isSeedProvidedFromEnv());
        $this->assertSame(12345, $this->testInstance->getFlakySeedFromEnv());
    }

    public function testSetsAndChecksGlobalSeed(): void
    {
        $this->assertFalse($this->testInstance->isFlakySeedInitialized());

        $seed = 54321;
        $this->testInstance->setFlakyTestSeed($seed);

        $this->assertTrue($this->testInstance->isFlakySeedInitialized());
        $this->assertSame($seed, $this->testInstance->getGlobalSeed());
    }

    public function FLAKY_SEEDs_random_number_generator(): void
    {
        $seed = 98765;
        $this->testInstance->setFlakyTestSeed($seed);

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

    public function testInitializeWithEnvironmentSeed(): void
    {
        putenv('FLAKY_SEED=42');

        $this->testInstance->initializeFlakySeed();

        $this->assertTrue($this->testInstance->isFlakySeedInitialized());
        $this->assertSame(42, $this->testInstance->getGlobalSeed());
    }

    public function testInitializeGeneratesSeedWhenNoEnvAndNoExistingSeed(): void
    {
        $this->testInstance->initializeFlakySeed();

        $this->assertTrue($this->testInstance->isFlakySeedInitialized());
        $this->assertIsInt($this->testInstance->getGlobalSeed());
        $this->assertGreaterThanOrEqual(0, $this->testInstance->getGlobalSeed());
    }

    public function testInitializeReusesExistingSeed(): void
    {
        $originalSeed = 777;
        $this->testInstance->setFlakyTestSeed($originalSeed);

        // Get some random numbers to verify the state
        $random1 = mt_rand();
        $random2 = mt_rand();

        // Initialize again - should re-seed but keep the same global seed
        $this->testInstance->initializeFlakySeed();

        $this->assertSame($originalSeed, $this->testInstance->getGlobalSeed());

        // Should get the same sequence again since we re-seeded
        $this->assertSame($random1, mt_rand());
        $this->assertSame($random2, mt_rand());
    }

    public function testCleanupLockFiles(): void
    {
        // Create some test files
        file_put_contents($this->testInstance->getLockFilePath(), 'test');
        file_put_contents($this->testInstance->getSeedFilePath(), '12345');

        $this->assertFileExists($this->testInstance->getLockFilePath());
        $this->assertFileExists($this->testInstance->getSeedFilePath());

        $this->testInstance->cleanupLockFiles();

        $this->assertFileDoesNotExist($this->testInstance->getLockFilePath());
        $this->assertFileDoesNotExist($this->testInstance->getSeedFilePath());
    }

    public function testSeedRandomnessSourcesThrowsExceptionWhenSeedNotInitialized(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Global seed is not initialized.');

        $this->testInstance->seedRandomnessSources();
    }
}

// Test class that uses the trait for testing purposes
class TestClassUsingManagesGlobalSeed
{
    use ManagesFlakyTestSeed;

    public function getGlobalSeed(): ?int
    {
        return static::$flakySeed;
    }

    public function resetGlobalSeed(): void
    {
        static::$flakySeed = null;
    }
}
