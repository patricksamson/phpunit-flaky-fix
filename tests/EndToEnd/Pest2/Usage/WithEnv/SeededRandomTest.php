<?php

declare(strict_types=1);

namespace PatrickSamson\PHPUnitFlakyFix\Tests\EndToEnd\Pest2\Usage\WithEnv;

use PHPUnit\Framework\TestCase;

final class SeededRandomTest extends TestCase
{
    public function testSeededMtRand(): void
    {
        $expectedNumber = 1328851649; // From the seed `1234567890`
        self::assertGreaterThanOrEqual($expectedNumber, mt_rand());
    }

    public function testReseededBetweenTests(): void
    {
        // This test will run with the same seed as the previous one
        $this->testSeededMtRand();
    }
}
