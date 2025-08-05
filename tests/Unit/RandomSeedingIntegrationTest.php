<?php

declare(strict_types=1);

namespace PatrickSamson\PHPUnitFlakyFix\Tests\Unit;

use PatrickSamson\PHPUnitFlakyFix\Concerns\ManagesFlakyTestSeed;
use PHPUnit\Framework\TestCase;

final class RandomSeedingIntegrationTest extends TestCase
{
    use ManagesFlakyTestSeed;

    protected function setUp(): void
    {
        $this->cleanupFiles();
    }

    protected function tearDown(): void
    {
        $this->cleanupFiles();
        putenv('FLAKY_SEED');
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

    public function testSameSeedProducesSameRandomSequence(): void
    {
        $seed = 42;

        // First run with the seed
        $this->setFlakyTestSeed($seed);
        $sequence1 = [
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand(),
            mt_rand(),
        ];

        // Second run with the same seed
        $this->setFlakyTestSeed($seed);
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

    public function testDifferentSeedsProduceDifferentSequences(): void
    {
        // First sequence with seed 100
        $this->setFlakyTestSeed(100);
        $sequence1 = [
            mt_rand(),
            mt_rand(),
            mt_rand(),
        ];

        // Second sequence with seed 200
        $this->setFlakyTestSeed(200);
        $sequence2 = [
            mt_rand(),
            mt_rand(),
            mt_rand(),
        ];

        // Sequences should be different
        $this->assertNotSame($sequence1, $sequence2);
    }

    public function testEnvironmentSeedProducesConsistentResults(): void
    {
        putenv('FLAKY_SEED=999');

        // Initialize with environment seed
        $this->initializeFlakySeed();
        $sequence1 = [
            mt_rand(),
            mt_rand(),
            mt_rand(),
        ];

        // Re-initialize with same environment seed
        $this->initializeFlakySeed();
        $sequence2 = [
            mt_rand(),
            mt_rand(),
            mt_rand(),
        ];

        // Should produce the same sequence
        $this->assertSame($sequence1, $sequence2);
    }
}
