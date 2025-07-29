<?php

declare(strict_types=1);

namespace PatrickSamson\PHPUnitFlakyFix\Tests\Unit;

use PatrickSamson\PHPUnitFlakyFix\Concerns\ManagesGlobalSeed;
use PHPUnit\Framework\TestCase;

final class RandomSeedingIntegrationTest extends TestCase
{
    use ManagesGlobalSeed;

    protected function setUp(): void
    {
        $this->cleanupFiles();
    }

    protected function tearDown(): void
    {
        $this->cleanupFiles();
        putenv('TEST_SEED');
    }

    private function cleanupFiles(): void
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

    public function test_same_seed_produces_same_random_sequence(): void
    {
        $seed = 42;

        // First run with the seed
        $this->setGlobalSeed($seed);
        $sequence1 = [
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand(),
        ];

        // Second run with the same seed
        $this->setGlobalSeed($seed);
        $sequence2 = [
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand(),
        ];

        // Sequences should be identical
        $this->assertSame($sequence1, $sequence2);
    }

    public function test_different_seeds_produce_different_sequences(): void
    {
        // First sequence with seed 100
        $this->setGlobalSeed(100);
        $sequence1 = [
            mt_rand(),
            mt_rand(),
            mt_rand(),
        ];

        // Second sequence with seed 200
        $this->setGlobalSeed(200);
        $sequence2 = [
            mt_rand(),
            mt_rand(),
            mt_rand(),
        ];

        // Sequences should be different
        $this->assertNotSame($sequence1, $sequence2);
    }

    public function test_environment_seed_produces_consistent_results(): void
    {
        putenv('TEST_SEED=999');

        // Initialize with environment seed
        $this->initializeGlobalSeed();
        $sequence1 = [
            mt_rand(),
            mt_rand(),
            mt_rand(),
        ];

        // Re-initialize with same environment seed
        $this->initializeGlobalSeed();
        $sequence2 = [
            mt_rand(),
            mt_rand(),
            mt_rand(),
        ];

        // Should produce the same sequence
        $this->assertSame($sequence1, $sequence2);
    }
}
