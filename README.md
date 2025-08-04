# PHPUnit Flaky Fix

[![Tests](https://github.com/patricksamson/phpunit-flaky-fix/workflows/Tests/badge.svg)](https://github.com/patricksamson/phpunit-flaky-fix/actions)

[![Latest Stable Version](https://poser.pugx.org/patricksamson/phpunit-flaky-fix/v/stable)](https://packagist.org/packages/patricksamson/phpunit-flaky-fix)
[![Total Downloads](https://poser.pugx.org/patricksamson/phpunit-flaky-fix/downloads)](https://packagist.org/packages/patricksamson/phpunit-flaky-fix)
[![Monthly Downloads](http://poser.pugx.org/patricksamson/phpunit-flaky-fix/d/monthly)](https://packagist.org/packages/patricksamson/phpunit-flaky-fix)

This package provides a PHPUnit extension that helps reproduce flaky tests by seeding the random number generator with a consistent value across test runs. This allows you to reproduce and debug flaky tests more easily.

## Compatibility

This package is compatible with the following versions of PHP and PHPUnit:
- **PHP :** `^8.1 || ^8.2 || ^8.3 || ^8.4`
- **PHPUnit :** `^10.0 || ^11.0 || ^12.0`

It has been tested with the following versions of popular tools that wrap PHPUnit:
- **Paratest :** `^7.0`
- **Pest :** `^2.0 || ^3.0`

It is compatible with any tool that uses `mt_rand()` for randomness in tests, such as [Faker](https://github.com/FakerPHP/Faker).

## Installation

```bash
composer require --dev patricksamson/phpunit-flaky-fix
```

You'll then need to register the extension in your `phpunit.xml` file:

```xml
<phpunit>
    <extensions>
        <bootstrap class="PatrickSamson\PHPUnitFlakyFix\FlakyFixExtension" />
    </extensions>
</phpunit>
```

## How It Works

The extension works by:

1. Generating a new, completely random seed at the start of your test suite execution, and displaying it in the output
2. Seeding PHP's random number generator (`mt_rand()`) with this value, before `setUp()` is called for each test

This ensures that your tests remain deterministic and reproducible, while still allowing for randomness between different test suite runs.
To reproduce a flaky test failure, simply re-run that specific test with the provided seed.

In practice, there is some additional complexity to ensure compatibility with parallel test execution and other PHPUnit features.

## Limitations

1. This extension only affects PHP's `mt_rand()` function and related randomness functions
2. It has no effect on time-based functions (e.g., `time()`, `date()`, ...), but that can be remediated using something like `Carbon::setTestNow($knownDate)`
3. It does not control randomness from other sources (e.g., database auto-increment values, UUID generation, external API calls, ...)
4. The seed is generated per test suite run, not per individual test
5. Some PHPUnit features or third-party tools might interfere with the seed generation process or the output display
