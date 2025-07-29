<?php

declare(strict_types=1);

namespace PatrickSamson\PHPUnitFlakyFix\Tests\EndToEnd\PHPUnit11\Usage\Default;

use PHPUnit\Framework\TestCase;

final class RandomTest extends TestCase
{
    public function test_mt_rand(): void
    {
        self::assertGreaterThanOrEqual(0, mt_rand());
    }
}
