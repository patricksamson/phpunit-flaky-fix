<?php

declare(strict_types=1);

namespace PatrickSamson\PHPUnitFlakyFix\Tests\EndToEnd\PHPUnit10\Usage\WithEnv;

use PHPUnit\Framework\TestCase;

final class SeededRandomTest extends TestCase
{
    public function testSeededMtRand(): void
    {
        $expectedNumber = 1328851649; // From the seed `1234567890`
        self::assertGreaterThanOrEqual($expectedNumber, mt_rand());
    }
}
