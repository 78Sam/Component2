<?php

declare(strict_types=1);

namespace ComponentPHP\Routing;

abstract class AbstractController
{
	final public function __construct(
		protected readonly Router $router,
	) {
	}

	protected function render(string $component)
	{
		$fullPath = COMPONENT_ROOT_DIR . '/App/Components/' . trim($component, '/\\');
		echo file_get_contents($fullPath);
	}
}
