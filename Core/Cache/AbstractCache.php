<?php

declare(strict_types=1);

namespace ComponentPHP\Cache;

abstract class AbstractCache
{
	protected const string CACHE_LINE_HEADER = "<?php\n\ndeclare(strict_types=1);\n\n\$_cacheLineData = ";

    private static array $store = [];

	public static function writeCache(CacheLine $cacheLine, mixed $data): void
	{
        self::$store[$cacheLine->value] = $data;
		file_put_contents($cacheLine->path(static::getDir()), self::CACHE_LINE_HEADER . var_export($data, return: true) . ";\n");
	}

	public static function readCache(CacheLine $cacheLine, mixed $default = null): mixed
	{
        if (\array_key_exists($cacheLine->value, self::$store))
        {
            return self::$store[$cacheLine->value];
        }

        $path = $cacheLine->path(static::getDir());
        if (!file_exists($path))
        {
            return $default;
        }

		include_once $path;
        self::$store[$cacheLine->value] = $_cacheLineData;

		return $_cacheLineData;
	}

	abstract protected static function getDir(): string;

    // TODO: This could be better than always writing to the file for every cache write
    // private function __destruct()
    // {
    //     foreach (self::$store as $key => $data)
    //     {
    //         $cacheLine = CacheLine::tryFrom($key);
    //         file_put_contents($cacheLine->path($this->getDir()), self::CACHE_LINE_HEADER . var_export($data, return: true) . ";\n");
    //     }
    // }
}
