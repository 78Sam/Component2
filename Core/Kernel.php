<?php

declare(strict_types=1);

namespace ComponentPHP;

use ComponentPHP\Routing\Router;

class Kernel
{
	private Router $router;

	public function __construct()
	{
		try
		{
			$this->router = new Router();
			$this->router->handleRequest();
		}
		catch (\Exception $exception)
		{
			$this->except($exception);
		}
	}

	private function except(\Exception $e): never
	{
		dump($e);

		exit();
	}
}
