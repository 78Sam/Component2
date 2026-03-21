<?php

declare(strict_types=1);

namespace ComponentPHP;

use ComponentPHP\Routing\AbstractController;
use ComponentPHP\Routing\Router;

class Kernel
{
	private Router $router;

	/** @var string[] $controllerClasses */
	private array $controllerClasses;

	public function __construct()
	{
		try
		{
			$this->router = new Router();
			$coreRequest = $this->router->getCoreRequest();
			dump($coreRequest);

			$this->controllerClasses = $this->getControllerClasses();
			dump($this->controllerClasses);
		}
		catch (\Exception $exception)
		{
			$this->except($exception);
		}
	}

	/**
	 * @return string[]
	 */
	private function getControllerClasses(): array
	{
		$controllerClasses = [];
		$controllersDirectoryFilePath = dirname(__DIR__) . "/App/Controllers";
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($controllersDirectoryFilePath));

		/** @var \SplFileInfo $file */
		foreach ($iterator as $file)
		{
			if ($file->isDir() || $file->getExtension() !== "php")
			{
				continue;
			}

			$fullPath = $file->getRealPath();
			if ($fullPath === false)
			{
				continue;
			}

			$classname = substr(str_replace(dirname(__DIR__) . '/', '', $fullPath), 0, -4);
			$classname = str_replace('/', '\\', $classname);
			if (!class_exists($classname))
			{
				continue;
			}
			
			try
			{
				$reflectionClass = new \ReflectionClass($classname);
				foreach ($reflectionClass->getMethods() as $method)
				{
					dump($method->getAttributes());
				}
			}
			catch (\ReflectionException)
			{
				continue;
			}

			$parentClass = $reflectionClass->getParentClass();
			if ($parentClass === false)
			{
				continue;
			}

			if ($parentClass->name === AbstractController::class)
			{
				$controllerClasses[] = $classname;
			}
		}

		return $controllerClasses;
	}

	private function except(\Exception $e): never
	{
		dump($e->getMessage());
		exit();
	}
}
