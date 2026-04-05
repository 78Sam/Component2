<?php

declare(strict_types=1);

namespace ComponentPHP\Cache\Components;

use ComponentPHP\Cache\AbstractCache;

class ComponentCache extends AbstractCache
{
	protected static function getDir(): string
	{
		return __DIR__;
	}
}
