<?php

namespace App\Support;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;

class WindowsSafeFilesystem extends Filesystem
{
    /**
     * Replace the given file with new content.
     *
     * On Windows, rename() can fail with "Access is denied" when an existing
     * target file is briefly locked by another process (AV/indexer/web server).
     * We retry and fall back to copy+unlink for better resilience.
     */
    public function replace($path, $content, $mode = null): void
    {
        clearstatcache(true, $path);

        $path = realpath($path) ?: $path;
        $tempPath = tempnam(dirname($path), basename($path));

        if ($tempPath === false) {
            throw new RuntimeException("Unable to create temporary file for [{$path}].");
        }

        if (! is_null($mode)) {
            chmod($tempPath, $mode);
        } else {
            chmod($tempPath, 0777 - umask());
        }

        file_put_contents($tempPath, $content);

        for ($attempt = 0; $attempt < 3; $attempt++) {
            if (@rename($tempPath, $path)) {
                return;
            }

            if ($this->exists($path)) {
                @chmod($path, 0777 - umask());
                @unlink($path);
            }

            usleep(50000);
        }

        if (@copy($tempPath, $path)) {
            @unlink($tempPath);

            return;
        }

        @unlink($tempPath);

        throw new RuntimeException("Unable to replace file at [{$path}].");
    }
}
