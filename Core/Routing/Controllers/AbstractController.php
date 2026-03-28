<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Controllers;

use ComponentPHP\Routing\Model\Response;
use ComponentPHP\Routing\Router;

abstract class AbstractController
{
	final public function __construct(
		protected readonly Router $router,
	) {
	}

	protected function render(string $component): Response
	{
		$fullPath = COMPONENT_ROOT_DIR . '/App/Components/' . trim($component, '/\\');

		return new Response(file_get_contents($fullPath));
	}
}
