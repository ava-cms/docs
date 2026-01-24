---
title: Testing
slug: testing
status: published
meta_title: Testing | Flat-file PHP CMS | Ava CMS
meta_description: Ava CMS includes a lightweight, zero-dependency test framework. Learn how to run tests, write test cases, and use release checks for maintainers.
excerpt: Ava CMS includes a lightweight, zero-dependency test framework for verifying core functionality. Run tests with the CLI and write your own test cases.
---

Ava CMS includes a lightweight, zero-dependency test framework for verifying core functionality. Tests are designed for maintainers and contributors working on the CMS itself.

This page documents the test framework exactly as it exists in the current Ava CMS codebase:

- The runner is implemented in `core/Testing/`.
- The CLI entrypoint is `./ava test` (implemented in `core/Cli/Application.php`).
- Tests live in `core/tests/`.

## Running Tests

Run the test suite from your project root:

```bash
./ava test
```

<pre><samp>  <span class="t-bold">Ava CMS Test Suite</span>
  <span class="t-dim">──────────────────────────────────────────────────</span>

  <span class="t-cyan">StrTest</span>

    <span class="t-green">✓</span> slug converts to lowercase
    <span class="t-green">✓</span> slug replaces spaces with separator
    <span class="t-green">✓</span> starts with returns true for match
    <span class="t-dim">...</span>

  <span class="t-cyan">ParserTest</span>

    <span class="t-green">✓</span> parse extracts frontmatter and content
    <span class="t-green">✓</span> parse handles multiple frontmatter fields
    <span class="t-dim">...</span>

  <span class="t-dim">──────────────────────────────────────────────────</span>
  <span class="t-bold">Tests:</span> <span class="t-green">383 passed</span> <span class="t-dim">(70ms)</span></samp></pre>

### Command Synopsis

```bash
./ava test [filter] [-q|--quiet] [--release] [-v|--verbose]
```

Supported flags/arguments:

- `filter` (optional): a single string used to filter the run. The runner first matches the class name (case-insensitive substring); if the class matches, the same filter is applied to method names to narrow which tests run within that class.
- `-q`, `--quiet`: reduce output.
- `--release`: include release-readiness tests under `core/tests/Release/`.
- `-v`, `--verbose`: accepted by the CLI, but currently does not change output (reserved for a future verbose mode).

### Quiet Mode

Run tests with minimal output (header + summary only):

```bash
./ava test --quiet
./ava test -q
```

<pre><samp>  <span class="t-bold">Ava CMS Test Suite</span>
  <span class="t-dim">──────────────────────────────────────────────────</span>
  <span class="t-bold">Tests:</span> <span class="t-green">383 passed</span> <span class="t-dim">(60ms)</span></samp></pre>

Useful for CI/CD pipelines or when you just want to know if tests pass.


## Test Structure

Tests live in `core/tests/` and are organised by component:

```
core/tests/
├── Admin/
│   ├── AuthTest.php           # Admin authentication
│   ├── ContentSecurityTest.php # Content Security Policy rules
│   ├── DebugTest.php          # Debug configuration and logging
│   └── MediaUploaderTest.php  # Media upload handling
├── Config/
│   └── ConfigTest.php         # Configuration access patterns
├── Content/
│   ├── IndexerRoutesTest.php  # Indexer route generation
│   ├── ItemTest.php           # Content item value object
│   ├── ParserTest.php         # Markdown/YAML parser
│   └── QueryTest.php          # Content query builder
├── Core/
│   └── UpdaterTest.php         # Updater behavior
├── Fields/
│   ├── BasicFieldsTest.php    # Basic field types
│   ├── ComplexFieldsTest.php  # Complex field types
│   ├── FieldRegistryTest.php  # Field registry
│   ├── SystemFieldsTest.php   # System fields
│   ├── TextFieldsTest.php     # Text fields
│   └── ValidationResultTest.php # Field validation result
├── Http/
│   ├── HttpsEnforcementTest.php  # HTTPS/localhost detection
│   ├── RequestTest.php        # HTTP request handling
│   └── ResponseTest.php       # HTTP response building
├── Plugins/
│   └── HooksTest.php          # Action/filter hook system
├── Rendering/
│   ├── ErrorPagesTest.php     # Error page rendering
│   ├── MarkdownTest.php       # CommonMark rendering
│   └── RawHtmlTest.php        # Raw HTML handling
├── Routing/
│   ├── RouteMatchTest.php     # Route match value object
│   └── RouterTest.php         # Router behavior
├── Shortcodes/
│   └── EngineTest.php         # Shortcode processing
├── Release/
│   └── ReleaseChecksTest.php   # Release-readiness checks (only with --release)
└── Support/
    ├── ArrTest.php            # Array utilities
    ├── PathTest.php           # Path utilities
    ├── StrTest.php            # String utilities
    └── UlidTest.php           # ULID generation
```


## Writing Tests

Tests are plain PHP classes discovered by filename + method name conventions.

Recommended approach: extend `Ava\Testing\TestCase`, which provides assertions and an injected `$app` instance.

Example:

```php
<?php

declare(strict_types=1);

namespace Ava\Tests\Support;

use Ava\Support\Str;
use Ava\Testing\TestCase;

final class StrTest extends TestCase
{
    public function testSlugConvertsToLowercase(): void
    {
        $this->assertEquals('hello-world', Str::slug('Hello World'));
    }

    public function testSlugRemovesSpecialCharacters(): void
    {
        $this->assertEquals('hello-world', Str::slug('Hello, World!'));
    }
}
```

### Test Discovery

The runner discovers tests by scanning `core/tests/` recursively:

- Files must end with `Test.php`
- The runner looks for a `namespace ...;` and a `class Name` in the file and constructs the class name from those
- All `public` methods whose name starts with `test` are executed
- Classes must be instantiable with no constructor arguments
- Abstract classes are skipped

Notes / quirks:

- A test class does **not** technically have to extend `TestCase` to run, but then you won’t have Ava CMS’s assertion helpers or the `$app` injection.
- If you define `setUp()` / `tearDown()`, define them as `public`. The runner calls them directly; non-public visibility will cause an error.
- If `setUp()` throws, that test is recorded as a failure and the test body is not run.
- If `tearDown()` throws, the error is ignored and does not fail the test.

### Available Assertions

| Assertion | Description |
|-----------|-------------|
| `assertTrue($value)` | Assert value is `true` |
| `assertFalse($value)` | Assert value is `false` |
| `assertEquals($expected, $actual)` | Assert values are equal (`==`) |
| `assertSame($expected, $actual)` | Assert values are identical (`===`) |
| `assertNotSame($expected, $actual)` | Assert values are not identical |
| `assertNotEquals($expected, $actual)` | Assert values differ |
| `assertNull($value)` | Assert value is `null` |
| `assertNotNull($value)` | Assert value is not `null` |
| `assertInstanceOf($class, $object)` | Assert object is instance of class |
| `assertIsArray($value)` | Assert value is an array |
| `assertIsString($value)` | Assert value is a string |
| `assertArrayHasKey($key, $array)` | Assert array has key |
| `assertContains($needle, $haystack)` | Assert array contains value |
| `assertCount($expected, $array)` | Assert array has count |
| `assertEmpty($value)` | Assert value is empty |
| `assertNotEmpty($value)` | Assert value is not empty |
| `assertStringContains($needle, $haystack)` | Assert string contains substring |
| `assertStringNotContains($needle, $haystack)` | Assert string does not contain substring |
| `assertStringStartsWith($prefix, $string)` | Assert string starts with prefix |
| `assertStringEndsWith($suffix, $string)` | Assert string ends with suffix |
| `assertMatchesRegex($pattern, $string)` | Assert string matches regex |
| `assertThrows($exception, $callback)` | Assert callback throws exception |
| `assertGreaterThan($expected, $actual)` | Assert actual > expected |
| `assertLessThan($expected, $actual)` | Assert actual < expected |
| `assertGreaterThanOrEqual($expected, $actual)` | Assert actual ≥ expected |
| `assertLessThanOrEqual($expected, $actual)` | Assert actual ≤ expected |
| `assertIsInt($value)` | Assert value is an integer |
| `assertIsBool($value)` | Assert value is a boolean |
| `assertArrayEquals($expected, $actual)` | Assert arrays equal after key-sorting |

### Skipping Tests

Skip a test conditionally:

```php
public function testRequiresExtension(): void
{
    if (!extension_loaded('igbinary')) {
        $this->skip('Requires igbinary extension');
    }
    
    // Test code here
}
```

`markSkipped($reason)` is an alias of `skip($reason)`.

Skipped tests are counted as skipped (not failed).

### What Counts as a Failure?

- Any `AssertionFailedException` (thrown by Ava CMS assertions) marks the test as failed.
- Any other uncaught `Throwable` inside a test method also marks it as failed.

Failures are listed at the end with the thrown message and file/line.


## Release Test Mode

By default, tests under `core/tests/Release/` are skipped.

To include them:

```bash
./ava test --release
```

These tests are intended for maintainers preparing a public release. See [Releasing](/docs/releasing).


## Test Philosophy

The test suite focuses on **unit testing core utilities** that have no external dependencies:

- **Support classes** (`Str`, `Arr`, `Path`, `Ulid`) - Pure functions
- **Value objects** (`Item`, `RouteMatch`, `Request`, `Response`) - Immutable data
- **Parsing** (`Parser`) - Frontmatter/Markdown extraction
- **Hooks system** - Filter/action registration and execution
- **Shortcodes** - Tag parsing and callback execution
- **Markdown** - CommonMark rendering behaviour
- **Config access** - Dot-notation array access patterns

Classes that require full `Application` context can still be tested: when you extend `TestCase`, Ava CMS injects an `Ava\Application` instance as `$this->app`.

However, Ava CMS’s test suite is still intentionally biased toward fast, deterministic tests.


## Continuous Integration

For CI pipelines, the test command returns appropriate exit codes:

```bash
./ava test
echo $?  # 0 = all passed, 1 = failures
```

Example GitHub Actions workflow:

```yaml
- name: Run tests
  run: ./ava test
```

<div class="related-docs">
<h2>Related Documentation</h2>
<ul>
<li><a href="/docs/cli#content-testing">CLI: Testing</a> — Test command options</li>
<li><a href="/docs/releasing">Releasing</a> — Release tests and workflows</li>
<li><a href="/docs/creating-plugins">Creating Plugins</a> — Testing plugin code</li>
</ul>
</div>
