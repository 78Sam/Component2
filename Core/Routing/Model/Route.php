<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Model;

readonly class Route
{
	public function __construct(
		public string $route,
		public string $name,
	) {
	}
}