# flyokai/amp-mate

> User docs → [`README.md`](README.md) · Agent quick-ref → [`CLAUDE.md`](CLAUDE.md) · Agent deep dive → [`AGENTS.md`](AGENTS.md)

AMPHP filesystem helpers: safe wrappers for file operations, async file locking, Amp File resource extraction.

See [AGENTS.md](AGENTS.md) for detailed module knowledge.

## Quick Reference

- **File ops**: `ampOpenFile()`, `ampUnlink()`, `ampFileExists()`, `ampDirExists()`, `ampMkdir()`
- **Locking**: `ampFlock()` with exponential backoff
- **Resource bridge**: `findAmpFileHandle()` extracts PHP resource from Amp File
- **Safe pattern**: `$safe=true` returns bool, `$safe=false` throws on error
