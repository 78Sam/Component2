<?php

declare(strict_types=1);

namespace ComponentPHP;

use ComponentPHP\Routing\Router;

class Kernel
{
	private Router $router;

	public function __construct()
	{
        $mode = CPHP_IS_DEV ? 'DEV' : 'PROD';
        cphpLog("Starting Kernel in {$mode} mode");
		try
		{
			$this->router = new Router();
            $this->router->init();
			$this->router->handleRequest();
		}
		catch (\Exception $exception)
		{
			$this->except($exception);
		}
	}

	private function except(\Exception $e): never
	{
        cphpLog($e->getMessage(), 'error');
		dump($e);

		exit();
	}
}
