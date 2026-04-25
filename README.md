# flyokai/amp-mate

> User docs → [`README.md`](README.md) · Agent quick-ref → [`CLAUDE.md`](CLAUDE.md) · Agent deep dive → [`AGENTS.md`](AGENTS.md)

> AMPHP filesystem helpers — safe wrappers, async file locking, and a bridge between AMPHP `File` objects and PHP resource handles.

## Features

- Safe / non-throwing variants of common filesystem operations
- `ampFlock()` with exponential-backoff retry, cancellation-aware
- `findAmpFileHandle()` — extracts the underlying `resource` from any AMPHP `File` driver
- All functions auto-loaded via Composer

## Installation

```bash
composer require flyokai/amp-mate
```

## Functions

All live in `src/functions/filesystem.php`:

| Function | Returns | Notes |
|----------|---------|-------|
| `findAmpFileHandle(File)` | `resource\|false` | Works with `StatusCachingFile`, `UvFile`, `EioFile`, `BlockingFile` |
| `ampFlock($handle, int $op, ?Cancellation, float $baseLatency = 0.01, int $maxAttempts = 10)` | `bool` (throws `TimeoutException` on exhaust) | Exponential backoff |
| `ampOpenFile(string $path, string $mode)` | `File\|false` | Safe open |
| `ampUnlink($file, bool $safe = true)` | `bool` |  |
| `ampFileExists(string $path)` | `bool` | `isFile && exists` |
| `ampDirExists(string $path)` | `bool` | `isDirectory && exists` |
| `ampMkdir(string $path, int $mode = 0755, bool $safe = true)` | `bool` |  |
| `ampChmod(string $path, int $mode, bool $safe = true)` | `bool` |  |

## Quick start

```php
use Amp\File;
use function Flyokai\AmpMate\{ampOpenFile, ampFlock, findAmpFileHandle};

$file = ampOpenFile('/tmp/lock.txt', 'c+');

if ($file === false) {
    throw new RuntimeException('cannot open');
}

$handle = findAmpFileHandle($file);
ampFlock($handle, LOCK_EX);   // suspends with backoff until acquired

$file->write('hello');
flock($handle, LOCK_UN);
$file->close();
```

## The `safe` pattern

Every operation that can fail accepts a `$safe` boolean:

- `$safe = true` (default) — return `false` / `bool` on failure
- `$safe = false` — throw the underlying exception

```php
ampMkdir('/tmp/foo', 0755);          // returns false on collision
ampMkdir('/tmp/foo', 0755, false);   // throws on collision
```

## Gotchas

- `ampFlock()` uses `Amp\delay()` between attempts — cooperative, but if `flock` itself blocks the OS it still blocks.
- Default `$maxAttempts = 10` with exponential backoff can take up to ~10 seconds before throwing.
- `findAmpFileHandle()` returns `false` silently for unsupported driver types.

## See also

- [`flyokai/magento-amp-mate`](../magento-amp-mate/README.md) — async cache backend built on top of these helpers

## License

MIT
