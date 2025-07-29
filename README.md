# PHPUnit Flaky Fix

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
