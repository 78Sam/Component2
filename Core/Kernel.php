<?php

declare(strict_types=1);

namespace ComponentPHP;

use ComponentPHP\Routing\Router;

class Kernel
{
	private Router $router;

	public function __construct()
	{
		define('COMPONENT_ROOT_DIR', str_replace(DIRECTORY_SEPARATOR, '/', dirname(__DIR__)));

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
