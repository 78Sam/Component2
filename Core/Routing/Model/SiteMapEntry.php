<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Model;

use ComponentPHP\Routing\Controllers\AbstractController;

readonly class SiteMapEntry
{
	/**
	 * @param class-string<AbstractController> $class
	 */
	public function __construct(
		public string $route,
		public string $name,
		public string $class,
		public string $method,
	) {
	}

	public static function __set_state($properties)
	{
		return new SiteMapEntry(...$properties);
	}
}
