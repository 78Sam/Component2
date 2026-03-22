<?php

declare(strict_types=1);

namespace ComponentPHP\Routing;

abstract class AbstractController
{
	final public function __construct(
		protected readonly Router $router,
	) {
	}

	public function pageNotFound(): void
	{
		echo 'Page not found';
	}
}
