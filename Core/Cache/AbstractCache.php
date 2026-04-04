<?php

declare(strict_types=1);

namespace ComponentPHP\Cache;

abstract class AbstractCache
{
	protected const string CACHE_LINE_HEADER = "<?php\n\ndeclare(strict_types=1);\n\n\$_cacheLineData = ";

	public function writeCache(CacheLine $cacheLine, mixed $data): void
	{
		file_put_contents($cacheLine->path($this->getDir()), self::CACHE_LINE_HEADER . var_export($data, return: true) . ";\n");
	}

	public function readCache(CacheLine $cacheLine, mixed $default = null): mixed
	{
		include_once $cacheLine->path($this->getDir());

		return $_cacheLineData ?? $default;
	}

	abstract protected function getDir(): string;
}
