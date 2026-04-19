<?php

declare(strict_types=1);

namespace ComponentPHP\Cache;

use ComponentPHP\Logging\Logger;
use ComponentPHP\Logging\Models\LoggingChannels;
use ComponentPHP\Logging\Models\LoggingLevel;

abstract class AbstractCache
{
    protected const string CACHE_LINE_HEADER = "<?php\n\ndeclare(strict_types=1);\n\n\$_cacheLineData = ";


    protected array $lineStates = [];

    /** @var array<string, mixed> $store */
    protected array $store = [];

    protected Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger(LoggingChannels::Cache);
    }

    public function __destruct()
    {
        $this->logger->log('Writing cache back to file');

        foreach ($this->store as $file => $cachedValue) {
            file_put_contents($file, self::CACHE_LINE_HEADER . var_export($cachedValue, return: true) . ";\n");
        }
    }

    public function readCache(string $cacheLine, mixed $default = null)
    {
        $this->lineStates[$cacheLine]['read'] = true;

        $path = $this->getCacheLinePath($cacheLine);
        if (\array_key_exists($path, $this->store)) {
            return $this->store[$path];
        }

        if (!file_exists($path)) {
            $this->logger->log("Failed to find cache file {$path}", level: LoggingLevel::Warning);

            return $default;
        }

        // TODO: Do we maybe want to ?? $default, issue is if we have saved 'null'
        include $path;
        $cachedValue = $_cacheLineData ?? '_undefined_cached_variable';
        if ($cachedValue === '_undefined_cached_variable')
        {
            $this->logger->log("Undefined variable \$_cacheLineData for line {$cacheLine}", level: LoggingLevel::Error);

            return $default;
        }

        $this->store[$cacheLine] = $_cacheLineData;
        $this->lineStates[$cacheLine]['read'] = true;

        return $this->store[$cacheLine];
    }

    public function writeCache(string $cacheLine, mixed $data): void
    {
        $hasRead = $this->lineStates[$cacheLine]['read'] ?? false;

        $this->logger->log("Writing to cache line {$cacheLine}");
        if (!$hasRead)
        {
            $this->logger->log("Writing to cache line {$cacheLine} before reading it", level: LoggingLevel::Warning);
        }
        $this->store[$this->getCacheLinePath($cacheLine)] = $data;
    }

    protected function getCacheLinePath(string $cacheLine): string
    {
        return "{$this->getDir()}/Lines/{$cacheLine}.php";
    }

    abstract protected function getDir(): string;
}
