<?php

declare(strict_types=1);

namespace ComponentPHP\Files;

use FilesystemIterator;

class FileSystem
{
    public const string PUBLIC_FOLDER = CPHP_ROOT_DIR . '/Public';

    public function toRelativePath(string $path): string
    {
        return explode('/Public/', $this->sanitisePath($path))[1];
    }

    public function sanitisePath(string $path): string
    {
        return str_replace(DIRECTORY_SEPARATOR, '/', $path);
    }

    /**
     * @return \Generator<string, \SplFileInfo, mixed, void>
     */
    public function iterate(string $path): \Generator
    {
        $path = ltrim($this->sanitisePath($path), '/');
        $fullPath = self::PUBLIC_FOLDER . "/{$path}";
        $recursiveDirectoryIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($fullPath, FilesystemIterator::SKIP_DOTS),
        );

        yield from $recursiveDirectoryIterator;
    }
}
