<?php

declare(strict_types=1);

namespace ComponentPHP\Routing;

use ComponentPHP\Routing\Attributes\Route;
use ComponentPHP\Routing\Exceptions\HostNotFoundException;
use ComponentPHP\Routing\Exceptions\PageNotFoundException;
use ComponentPHP\Routing\Exceptions\RouteAlreadyExistsException;
use ComponentPHP\Routing\Exceptions\UriNotFoundException;
use ComponentPHP\Routing\Exceptions\UrlParseException;
use ComponentPHP\Routing\Model\Request;
use ComponentPHP\Routing\Model\SiteMapEntry;

class Router
{
	private Request $coreRequest;

	/** @var array{routes: SiteMapEntry[], names: SiteMapEntry[]} $siteMap */
	private array $siteMap;

	/**
	 * @throws UrlParseException
	 * @throws HostNotFoundException
	 * @throws UriNotFoundException
	 */
	public function __construct()
	{
		$this->siteMap = ['routes' => [], 'names' => []];

		$this->parseCoreRequest();
		$this->drawSiteMap();
	}

	/**
	 * @throws 
	 * @return void
	 */
	public function handleRequest(?Request $request = null): void
	{
		$request ??= $this->coreRequest;
		if (\array_key_exists($request->route, $this->siteMap['routes']))
		{
			$siteMapEntry = $this->siteMap['routes'][$request->route];
			$abstractController = new ($siteMapEntry->class)(router: $this);
			$abstractController->{$siteMapEntry->method}();

			return;
		}

		throw new PageNotFoundException($request->route, "Could not find the page");
	}

	public function getUrlFor(string $name): ?string
	{
		if (!\array_key_exists($name, $this->siteMap['names']))
		{
			return null;
		}

		$scheme = $this->coreRequest->scheme;
		$host = $this->coreRequest->host;
		$port = $this->coreRequest->port === null ? '' : ":{$this->coreRequest->port}";
		$route = $this->siteMap['names'][$name]->route;

		return "{$scheme}://{$host}{$port}{$route}";
	}

	/**
	 * @throws UrlParseException
	 * @throws HostNotFoundException
	 * @throws UriNotFoundException
	 */
	private function parseCoreRequest(): void
	{
		$scheme = ($_SERVER['HTTPS'] ?? 'off') === 'on' ? 'https' : 'http';

		$host = $_SERVER['HTTP_HOST'] ?? null;
		if ($host === null)
		{
			throw new HostNotFoundException("Unable to parse host");
		}

		$uri = $_SERVER['REQUEST_URI'] ?? null;
		if ($uri === null)
		{
			throw new UriNotFoundException("Unable to parse uri");
		}

		$routeAndQuery = explode('?', $uri, limit: 2);
		$route = '/' . trim($routeAndQuery[0], '/');

		$this->coreRequest = new Request(
			scheme: $scheme,
			host: $host,
			route: $route,
			query: $routeAndQuery[1] ?? null,
		);
	}

	/**
	 * @throws RouteAlreadyExistsException
	 */
	private function drawSiteMap(): void
	{
		$rootDir = dirname(dirname(__DIR__));
		$controllersDirectoryFilePath = "{$rootDir}/App/Controllers";
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

			$classname = substr(str_replace("{$rootDir}/", '', $fullPath), 0, -4);
			$classname = str_replace(DIRECTORY_SEPARATOR, '\\', $classname);
			if (!class_exists($classname))
			{
				continue;
			}
			
			try
			{
				$reflectionClass = new \ReflectionClass($classname);
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

			if ($parentClass->name !== AbstractController::class)
			{
				continue;
			}

			foreach ($reflectionClass->getMethods() as $method)
			{
				foreach ($method->getAttributes(Route::class) as $routeAttribute)
				{
					$routeAttributeInstance = $routeAttribute->newInstance();
					$route = $routeAttributeInstance->route;
					$name = $routeAttributeInstance->name;

					$siteMapEntry = new SiteMapEntry(
						route: $route,
						name: $name,
						class: $reflectionClass->name,
						method: $method->name,
					);

					if (\array_key_exists($route, $this->siteMap['routes']))
					{
						throw new RouteAlreadyExistsException(route: $route, name: $name, message: 'Route already exists');
					}

					if (\array_key_exists($name, $this->siteMap['names']))
					{
						throw new RouteAlreadyExistsException(route: $route, name: $name, message: 'Route name already exists');
					}

					$this->siteMap['routes'][$route] = $siteMapEntry;
					$this->siteMap['names'][$name] = $siteMapEntry;
				}
			}
		}
	}
}
