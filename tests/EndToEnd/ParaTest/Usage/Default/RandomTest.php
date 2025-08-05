<?php

declare(strict_types=1);

namespace PatrickSamson\PHPUnitFlakyFix\Tests\EndToEnd\ParaTest\Usage\Default;

use PHPUnit\Framework\TestCase;

final class RandomTest extends TestCase
{
    public function testMtRand(): void
    {
        self::assertGreaterThanOrEqual(0, mt_rand());
    }
}
