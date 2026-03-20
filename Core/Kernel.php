<?php

declare(strict_types=1);

namespace ComponentPHP;

use ComponentPHP\Routing\Router;

class Kernel
{
	private Router $router;

	public function __construct()
	{
		$this->router = new Router();
		$coreRequest = $this->router->getCoreRequest();

		dump($coreRequest);
		dump($coreRequest->get('sam', 'hehe no'));
		dump($coreRequest->get('walla', 'hehe no 2'));
	}
}
