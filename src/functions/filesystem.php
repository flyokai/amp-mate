<?php

namespace Flyokai\AmpMate;

use Amp\Cancellation;
use Amp\File\File as AmpFile;
use Amp\File\FilesystemException;
use Amp\TimeoutException;
use function Amp\delay;
use function \Flyokai\DataMate\getObjectPrivateProperty;

function findAmpFileHandle(AmpFile $file)
{
    return match(get_class($file)) {
        \Amp\File\Driver\StatusCachingFile::class =>
        findAmpFileHandle(getObjectPrivateProperty($file, 'file')),
        \Amp\File\Driver\UvFile::class, \Amp\File\Driver\EioFile::class =>
        getObjectPrivateProperty($file, 'fh'),
        \Amp\File\Driver\BlockingFile::class =>
        getObjectPrivateProperty($file, 'handle'),
        default => false
    };
}

function ampFlock($handle, $operation,
                  ?Cancellation $cancellation = null, float $baseLatency = 0.01, int $maxAttempts = 10
) {
    if ($handle instanceof AmpFile) {
        $handle = findAmpFileHandle($handle);
    }
    if (!is_resource($handle)) {
        throw new \InvalidArgumentException(__('Handle must be a resource'));
    }
    $attempt = 0;
    while ($attempt++<$maxAttempts) {
        if (flock($handle, $operation | LOCK_NB)) {
            return true;
        }
        $__delay = $baseLatency*pow(2, $attempt-1);
        delay($__delay, true, $cancellation);
    }
    throw new TimeoutException(__('Failed to acquire lock after %1 attempts.', $maxAttempts));
}

function ampOpenFile(string $path, string $mode): bool|AmpFile
{
    try {
        return \Amp\File\openFile($path, $mode);
    } catch (FilesystemException $e) {
        return false;
    }
}

function ampUnlink($file, $safe=true)
{
    try {
        \Amp\File\deleteFile($file);
        return true;
    } catch (FilesystemException $e) {
        if (!$safe) {
            throw $e;
        }
        return false;
    }
}

function ampFileExists($path): bool
{
    return \Amp\File\isFile($path) && \Amp\File\exists($path);
}

function ampDirExists($path): bool
{
    return \Amp\File\isDirectory($path) && \Amp\File\exists($path);
}

function ampMkdir($path, $mode, $safe=true)
{
    try {
        \Amp\File\createDirectory($path, $mode);
        return true;
    } catch (FilesystemException $e) {
        if (!$safe) {
            throw $e;
        }
        return false;
    }
}

function ampChmod($path, $mode, $safe=true)
{
    try {
        \Amp\File\createDirectory($path, $mode);
        return true;
    } catch (FilesystemException $e) {
        if (!$safe) {
            throw $e;
        }
        return false;
    }
}
