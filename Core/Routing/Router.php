<?php

declare(strict_types=1);

namespace ComponentPHP\Routing;

use ComponentPHP\Cache\CacheLine;
use ComponentPHP\Cache\Routing\RoutingCache;
use ComponentPHP\Routing\Attributes\Route;
use ComponentPHP\Routing\Controllers\AbstractController;
use ComponentPHP\Routing\Exceptions\InvalidResponseException;
use ComponentPHP\Routing\Exceptions\PageNotFoundException;
use ComponentPHP\Routing\Exceptions\RouteAlreadyExistsException;
use ComponentPHP\Routing\Exceptions\RoutingException;
use ComponentPHP\Routing\Exceptions\UrlParseException;
use ComponentPHP\Routing\Model\Request;
use ComponentPHP\Routing\Model\Response;
use ComponentPHP\Routing\Model\SiteMapEntry;

class Router
{
	/** @var string[] CONTROLLER_DIRECTORIES */
	public const array CONTROLLER_DIRECTORIES = [
		COMPONENT_ROOT_DIR . '/App/Controllers',
		COMPONENT_ROOT_DIR . '/Core/Routing/Controllers',
	];

	/** @var Request[] $previousRequests */
	public array $previousRequests = [];

	private RoutingCache $cache;

	private ?Request $coreRequest = null;

	/** @var array{routes: array<string, SiteMapEntry>, names: array<string, SiteMapEntry>} $siteMap */
	private array $siteMap = ['routes' => [], 'names' => []];

	public function __construct()
	{
		$this->cache = new RoutingCache();
	}

	/**
	 * @throws RoutingException
	 */
	public function init()
	{
		$this->coreRequest = $this->parseCoreRequest();

		if (IS_DEV)
		{
			$this->drawFullSiteMap();

			return;
		}

		// If we are running in production, read the sitemap from cache

		$cacheValue = $this->cache->readCache(CacheLine::SiteMap);
		if ($cacheValue === null)
		{
			$this->drawFullSiteMap();
		}
		else
		{
			$this->siteMap = $cacheValue;
		}
	}

	/**
	 * Create the request object from server parameters
	 *
	 * @throws UrlParseException
	 */
	private function parseCoreRequest(): Request
	{
		$scheme = ($_SERVER['HTTPS'] ?? 'off') === 'on' ? 'https' : 'http';

		$host = $_SERVER['HTTP_HOST'] ?? null;
		if ($host === null)
		{
			throw new UrlParseException('$_SERVER["HTTP_HOST"] is null', code: 500);
		}

		$uri = $_SERVER['REQUEST_URI'] ?? null;
		if ($uri === null)
		{
			throw new UrlParseException('$_SERVER["REQUEST_URI"] is null', code: 500);
		}

		$routeAndQuery = explode('?', $uri, limit: 2);
		$route = '/' . trim($routeAndQuery[0], '/');

		return new Request(
			scheme: $scheme,
			host: $host,
			route: $route,
			query: $routeAndQuery[1] ?? null,
		);
	}

	/**
	 * Take the core request and call the corresponding controller method if there is one
	 * 
	 * @throws InvalidResponseException
	 * @throws PageNotFoundException
	 */
	public function handleRequest(?Request $request = null): void
	{
		$request ??= $this->coreRequest;
		if (\array_key_exists($request->route, $this->siteMap['routes']))
		{
			$siteMapEntry = $this->siteMap['routes'][$request->route];
			$abstractController = new ($siteMapEntry->class)(router: $this);

			$response = $abstractController->{$siteMapEntry->method}();
			if (!$response instanceof Response)
			{
				throw new InvalidResponseException('Controllers should return ' . Response::class);
			}

			$this->handleResponse($response);

			return;
		}

		throw new PageNotFoundException($request->route, 'Page not found', code: 404);
	}

	private function handleResponse(Response $response): void
	{
		http_response_code($response->responseCode);
		echo $response->content;

		/** @var ?Request[] $cachedRequests */
		$cachedRequests = $this->cache->readCache(CacheLine::Requests);
		if ($cachedRequests)
		{
			$this->previousRequests = $cachedRequests;
		}

		$this->previousRequests = array_merge([$this->coreRequest], $this->previousRequests);
		$this->cache->writeCache(CacheLine::Requests, $this->previousRequests);
	}

	// ! Util

	private function drawFullSiteMap(): void
	{
		foreach (self::CONTROLLER_DIRECTORIES as $directory)
		{
			$this->drawSiteMap($directory);
		}
		$this->cache->writeCache(CacheLine::SiteMap, $this->siteMap);
	}

	/**
	 * @throws RouteAlreadyExistsException
	 */
	private function drawSiteMap(string $searchSpace): void
	{
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($searchSpace));

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

			$classname = pathToClass($fullPath);
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
