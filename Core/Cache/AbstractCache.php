<?php

declare(strict_types=1);

namespace ComponentPHP\Cache;

use ComponentPHP\Logging\Logger;
use ComponentPHP\Logging\Models\LoggingChannels;
use ComponentPHP\Logging\Models\LoggingLevel;

abstract class AbstractCache
{
    protected const string CACHE_LINE_HEADER = "<?php\n\ndeclare(strict_types=1);\n\n\$_cacheLineData = ";

    /** @var array<string, array{data: mixed, state: string}> $store */
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
            if ($cachedValue['state'] !== 'write') {
                continue;
            }

            file_put_contents($file, self::CACHE_LINE_HEADER . "{$this->exportData($cachedValue['data'])};\n");
        }
    }

    abstract protected function getDir(): string;

    public function writeCache(string $cacheLine, mixed $data): void
    {
        $this->logger->log("Writing to cache line {$cacheLine}");

        $path = $this->getCacheLinePath($cacheLine);
        $hasRead = ($this->store[$path]['state'] ?? null) === 'read';
        if (!$hasRead) {
            $this->logger->log("Writing to cache line {$cacheLine} before reading it", level: LoggingLevel::Warning);
        }

        $this->writeStore($data, $path, 'write');
    }

    protected function readCache(string $cacheLine, mixed $default = null): mixed
    {
        $path = $this->getCacheLinePath($cacheLine);
        if (\array_key_exists($path, $this->store)) {
            return $this->store[$path]['data'];
        }

        if (!file_exists($path)) {
            $this->logger->log("Failed to find cache file {$path}", level: LoggingLevel::Warning);

            return $default;
        }

        // TODO(Sam): Do we maybe want to ?? $default, issue is if we have saved 'null'
        include $path;
        $cachedValue = $_cacheLineData ?? null;
        if ($cachedValue === null) {
            $this->logger->log("Undefined variable \$_cacheLineData for line {$cacheLine}", level: LoggingLevel::Error);

            return $default;
        }

        return $this->writeStore($cachedValue, $path, 'read');
    }

    protected function getCacheLinePath(string $cacheLine): string
    {
        return "{$this->getDir()}/Lines/{$cacheLine}.php";
    }

    private function writeStore(mixed $data, string $path, string $type): mixed
    {
        $this->store[$path]['data'] = $data;
        $this->store[$path]['state'] = $type;

        return $data;
    }

    private function exportData(mixed $data): mixed
    {
        if (is_array($data)) {
            $items = [];
            foreach ($data as $key => $value) {
                $items[] = "{$this->exportData($key)} => {$this->exportData($value)}";
            }

            return '[' . implode(', ', $items) . ']';
        }

        if (is_object($data)) {
            if ($data instanceof Cacheable) {
                $properties = $this->exportData($data->out());
                $class = $data::class;

                return "{$class}::in({$properties})";
            }

            return var_export($data, return: true);
        }

        return is_string($data) ? var_export($data, return: true) : $data;
    }
}
