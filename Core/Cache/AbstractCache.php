<?php

declare(strict_types=1);

namespace ComponentPHP\Cache;

use ComponentPHP\Cache\CacheLine;

abstract class AbstractCache
{
    protected const string CACHE_LINE_HEADER = "<?php\n\ndeclare(strict_types=1);\n\n\$_cacheLineData = ";

    /** @var array<string, mixed> $store */
    protected array $store = [];

    public function __destruct()
    {
        foreach ($this->store as $file => $cachedValue) {
            file_put_contents($file, self::CACHE_LINE_HEADER . var_export($cachedValue, return: true) . ";\n");
        }
    }

    public function readCache(CacheLine $cacheLine, mixed $default = null)
    {
        $path = $cacheLine->path($this->getDir());
        if (\array_key_exists($path, $this->store)) {
            return $this->store[$cacheLine->value];
        }

        if (!file_exists($path)) {
            cphpLog("Failed to find cache file {$path}", level: 'warning');

            return $default;
        }

        // TODO: Do we maybe want to ?? $default, issue is if we have saved 'null'
        include_once $path;
        $this->store[$cacheLine->value] = $_cacheLineData;

        return $this->store[$cacheLine->value];
    }

    public function writeCache(CacheLine $cacheLine, mixed $data): void
    {
        $this->store[$cacheLine->path($this->getDir())] = $data;
    }

    abstract protected function getDir(): string;
}
