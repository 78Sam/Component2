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
use ComponentPHP\Routing\Model\SiteMap;
use ComponentPHP\Routing\Model\SiteMapEntry;

class Router
{
	/** @var string[] CONTROLLER_DIRECTORIES */
	public const array CONTROLLER_DIRECTORIES = [
		CPHP_ROOT_DIR . '/App/Controllers',
		CPHP_ROOT_DIR . '/Core/Site/Controllers',
	];

	/** @var Request[] $previousRequests */
	public array $previousRequests = [];

	private ?Request $coreRequest = null;

	private SiteMap $siteMap;

	public function __construct()
	{
        $this->siteMap = new SiteMap();
	}

	/**
	 * @throws RoutingException
	 */
	public function init()
	{
        cphpLog('Starting router');
		$this->coreRequest = $this->parseCoreRequest();

		if (CPHP_IS_DEV)
		{
			$this->drawFullSiteMap();

			return;
		}

		// If we are running in production, read the sitemap from cache

		$cacheValue = RoutingCache::readCache(CacheLine::SiteMap);
		if ($cacheValue === null)
		{
            cphpLog('Failed to read cached site map for prod build', level: 'warning');
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

        $hostAndPort = explode(':', $host);
        $host = $hostAndPort[0];
        $port = null;
        if (\count($hostAndPort) === 2)
        {
            $port = (int) $hostAndPort[1];
        }

		$uri = $_SERVER['REQUEST_URI'] ?? null;
		if ($uri === null)
		{
			throw new UrlParseException('$_SERVER["REQUEST_URI"] is null', code: 500);
		}

		$routeAndQuery = explode('?', $uri, limit: 2);
		$route = '/' . trim($routeAndQuery[0], '/');

		$request = new Request(
			scheme: $scheme,
			host: $host,
			route: $route,
            time: $_SERVER["REQUEST_TIME"] ?? -1,
			query: $routeAndQuery[1] ?? null,
            port: (int) $port,
		);

        cphpLog("Parsed request: {$request}");

        return $request;
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

        $siteMapEntry = $this->siteMap->findByRoute($request->route);
        if ($siteMapEntry === null)
        {
            throw new PageNotFoundException($request->route, "Page not found '{$request->route}'", code: 404);
        }

        $abstractController = new ($siteMapEntry->class)(router: $this);
        $response = $abstractController->{$siteMapEntry->method}();
        if (!$response instanceof Response)
        {
            throw new InvalidResponseException('Controllers should return ' . Response::class, code: 500);
        }

        $this->handleResponse($response);
	}

	private function handleResponse(Response $response): void
	{
		http_response_code($response->responseCode);
		echo $response->content;

		/** @var ?Request[] $cachedRequests */
		$cachedRequests = RoutingCache::readCache(CacheLine::Requests, default: null);
		if ($cachedRequests !== null)
		{
			$this->previousRequests = $cachedRequests;
		}

		$this->previousRequests = [$this->coreRequest, ...array_slice($this->previousRequests, 0, 9)];
        cphpLog('Handling response');
		RoutingCache::writeCache(CacheLine::Requests, $this->previousRequests);
	}

	// ! Util

	private function drawFullSiteMap(): void
	{
        cphpLog('Drawing full site map');
		foreach (self::CONTROLLER_DIRECTORIES as $directory)
		{
			$this->drawSiteMap($directory);
		}
		RoutingCache::writeCache(CacheLine::SiteMap, $this->siteMap);
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

                    if (!$this->siteMap->addSiteMapEntry($siteMapEntry))
                    {
                        throw new RouteAlreadyExistsException(route: $route, name: $name, message: 'Route already exists', code: 500);
                    }
				}
			}
		}
	}
}
