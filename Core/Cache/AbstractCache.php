<?php

declare(strict_types=1);

namespace ComponentPHP\Cache;

abstract class AbstractCache
{
	protected const string CACHE_LINE_HEADER = "<?php\n\ndeclare(strict_types=1);\n\n\$_cacheLineData = ";

    /** @var array<string, array{data: mixed, dir: string}> $store */
    private static array $store = [];
    
    private static bool $registeredShutdown = false;

	public static function writeCache(CacheLine $cacheLine, mixed $data): void
	{
        self::writeStore($cacheLine, $data);
        if (!self::$registeredShutdown)
        {
            register_shutdown_function([self::class, 'writeBack']);
            self::$registeredShutdown = true;
        }
	}

	public static function readCache(CacheLine $cacheLine, mixed $default = null): mixed
	{
        if (\array_key_exists($cacheLine->value, self::$store))
        {
            return self::$store[$cacheLine->value]['data'];
        }

        $path = $cacheLine->path(static::getDir());
        if (!file_exists($path))
        {
            return $default;
        }

		include_once $path;
        self::writeStore($cacheLine, $_cacheLineData);

		return $_cacheLineData;
	}

    protected static function writeStore(CacheLine $cacheLine, mixed $data): void
    {
        self::$store[$cacheLine->value] = ['data' => $data, 'dir' => $cacheLine->path(static::getDir())];
    }

	abstract protected static function getDir(): string;

    protected static function writeBack()
    {
        foreach (self::$store as $key => $data)
        {
            file_put_contents($data['dir'], self::CACHE_LINE_HEADER . var_export($data['data'], return: true) . ";\n");
        }
    }
}
