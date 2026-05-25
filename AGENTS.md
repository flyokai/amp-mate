# flyokai/amp-mate

> User docs → [`README.md`](README.md) · Agent quick-ref → [`CLAUDE.md`](CLAUDE.md) · Agent deep dive → [`AGENTS.md`](AGENTS.md)

AMPHP filesystem helpers bridging async Amp File API with traditional PHP operations.

## Functions (auto-loaded)

All in `src/functions/filesystem.php`:

| Function | Purpose | Returns |
|----------|---------|---------|
| `findAmpFileHandle(AmpFile)` | Extracts PHP resource from Amp File objects (StatusCachingFile, UvFile, EioFile, BlockingFile) | resource or false |
| `ampFlock($handle, $op, ?Cancellation, $baseLatency, $maxAttempts)` | Async file locking with exponential backoff retry (default: 10 attempts, 0.01s base) | bool, throws TimeoutException |
| `ampOpenFile(string, string)` | Safe file open wrapper | AmpFile or false |
| `ampUnlink($file, $safe)` | Safe file deletion | bool |
| `ampFileExists($path)` | Check regular file exists (isFile AND exists) | bool |
| `ampDirExists($path)` | Check directory exists (isDirectory AND exists) | bool |
| `ampMkdir($path, $mode, $safe)` | Safe directory creation | bool |
| `ampChmod($path, $mode, $safe)` | Permission setting | bool |

## Patterns

- **Safe wrapper pattern**: `$safe` parameter controls bool return (true) vs exception throw (false)
- **Resource adaptation**: Bridges AMPHP File objects to PHP resource handles via reflection (`getObjectPrivateProperty` from data-mate)
- **Exponential backoff**: `ampFlock()` retries with 2^attempt multiplier

## Gotchas

- `ampFlock()` uses `Amp\delay()` between attempts — cooperative but not truly async
- `findAmpFileHandle()` returns `false` silently for unsupported driver types
- 10 flock attempts with exponential backoff can take up to ~10 seconds total
