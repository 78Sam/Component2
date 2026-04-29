<?php

declare(strict_types=1);

namespace ComponentPHP\Routing;

use ComponentPHP\Cache\Routing\RoutingCache;
use ComponentPHP\Logging\Logger;
use ComponentPHP\Logging\Models\LoggingChannels;
use ComponentPHP\Logging\Models\LoggingLevel;
use ComponentPHP\Routing\Attributes\Route;
use ComponentPHP\Routing\Controllers\AbstractController;
use ComponentPHP\Routing\Exceptions\InvalidResponseException;
use ComponentPHP\Routing\Exceptions\PageNotFoundException;
use ComponentPHP\Routing\Exceptions\RouteAlreadyExistsException;
use ComponentPHP\Routing\Exceptions\UrlParseException;
use ComponentPHP\Routing\Models\Request;
use ComponentPHP\Routing\Models\Response;
use ComponentPHP\Routing\Models\SiteMap;
use ComponentPHP\Routing\Models\SiteMapEntry;
use ComponentPHP\Utility\Config;

class Router
{
    /** @var string[] CONTROLLER_DIRECTORIES */
    public const array CONTROLLER_DIRECTORIES = [
        Config::ROOT_DIR . '/App/Controllers',
        Config::ROOT_DIR . '/Core/Site/Controllers',
    ];

    public const int MAX_CACHED_REQUESTS = 10;

    /** @var Request[] $previousRequests */
    public array $previousRequests = [];

    private Logger $logger;
    private Request $coreRequest;
    private RoutingCache $routingCache;
    private SiteMap $siteMap;

    public function __construct()
    {
        $this->logger = new Logger(channel: LoggingChannels::Router);
        $this->routingCache = new RoutingCache();
        $this->siteMap = new SiteMap();

        $this->logger->log('Starting router');
        $this->coreRequest = $this->parseCoreRequest();

        if (Config::IS_DEV) {
            $this->drawFullSiteMap();

            return;
        }

        // If we are running in production, read the sitemap from cache

        $cacheValue = $this->routingCache->readSiteMap();
        if ($cacheValue === null) {
            $this->logger->log('Failed to read cached site map for prod build', level: LoggingLevel::Warning);
            $this->drawFullSiteMap();

            return;
        }

        $this->siteMap = $cacheValue;
    }

    /**
     * Create the request object from server parameters
     *
     * @throws UrlParseException
     */
    private function parseCoreRequest(): Request
    {
        $hostAndPort = $_SERVER['HTTP_HOST'] ?? null;
        if ($hostAndPort === null) {
            throw new UrlParseException('$_SERVER["HTTP_HOST"] is null', code: 500);
        }
        $hostAndPort = explode(':', $hostAndPort);
        $host = $hostAndPort[0];
        $port = \count($hostAndPort) === 2 ? (int) $hostAndPort[1] : null;

        $request = new Request(
            scheme: ($_SERVER['https'] ?? null) !== null ? 'https' : 'http',
            host: $host,
            route: '/' . trim($_SERVER['PATH_INFO'] ?? '/', '/'),
            get: $_GET,
            post: $_POST,
            method: $_SERVER['REQUEST_METHOD'],
            time: $_SERVER['REQUEST_TIME'] ?? -1,
            port: $port,
        );

        $this->logger->log("Parsed request: {$request}");

        return $request;
    }

    /**
     * Take the core request and call the corresponding controller method if there is one
     *
     * @throws InvalidResponseException
     * @throws PageNotFoundException
     */
    public function handleRequest(?Request $request = null): Response
    {
        $request ??= $this->coreRequest;

        $siteMapEntry = $this->siteMap->findByRoute($request->route);
        if ($siteMapEntry === null) {
            throw new PageNotFoundException($request->route, "Page not found '{$request->route}'", code: 404);
        }

        // Does the route support the HTTP verb, e.g. POST, PATCH
        if ($siteMapEntry->HTTPVerbs !== [] && !\in_array($request->method, $siteMapEntry->HTTPVerbs, true)) {
            throw new PageNotFoundException(
                $request->route,
                "Page not found '{$request->route}' for {$request->method}",
                code: 404,
            );
        }

        $this->logger->log("Calling route {$siteMapEntry->class}::{$siteMapEntry->method}");

        $abstractController = new $siteMapEntry->class(router: $this, request: $request);
        $response = $abstractController->{$siteMapEntry->method}();
        if (!$response instanceof Response) {
            throw new InvalidResponseException('Controllers should return ' . Response::class, code: 500);
        }

        return $response;
    }

    public function handleResponse(Response $response): void
    {
        $this->logger->log('Handling response');

        http_response_code($response->responseCode);
        echo "{$response->content}";

        if (str_starts_with($this->coreRequest->route, '/_debug')) {
            return;
        }

        $cachedRequests = $this->routingCache->readRequests();
        if ($cachedRequests !== null) {
            $this->previousRequests = $cachedRequests;
        }

        $this->previousRequests = [
            $this->coreRequest,
            ...\array_slice($this->previousRequests, 0, self::MAX_CACHED_REQUESTS - 1),
        ];
        $this->routingCache->writeCache('Requests', $this->previousRequests);
    }

    // ! Util

    private function drawFullSiteMap(): void
    {
        $this->logger->log('Drawing full site map');

        foreach (self::CONTROLLER_DIRECTORIES as $directory) {
            $this->drawSiteMap($directory);
        }
        $this->routingCache->writeCache('SiteMap', $this->siteMap);
    }

    /**
     * @throws RouteAlreadyExistsException
     */
    private function drawSiteMap(string $searchSpace): void
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($searchSpace));

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isDir() || $file->getExtension() !== 'php') {
                continue;
            }

            $fullPath = $file->getRealPath();
            if ($fullPath === false) {
                continue;
            }

            $classname = path_to_class($fullPath);
            if (!class_exists($classname)) {
                continue;
            }

            try {
                $reflectionClass = new \ReflectionClass($classname);
            } catch (\ReflectionException) {
                continue;
            }

            $parentClass = $reflectionClass->getParentClass();
            if ($parentClass === false) {
                continue;
            }

            if ($parentClass->name !== AbstractController::class) {
                continue;
            }

            foreach ($reflectionClass->getMethods() as $method) {
                foreach ($method->getAttributes(Route::class) as $routeAttribute) {
                    $routeAttributeInstance = $routeAttribute->newInstance();
                    $route = $routeAttributeInstance->route;
                    $name = $routeAttributeInstance->name;

                    $siteMapEntry = new SiteMapEntry(
                        route: $route,
                        name: $name,
                        class: $reflectionClass->name,
                        method: $method->name,
                        HTTPVerbs: $routeAttributeInstance->HTTPVerbs,
                    );

                    if (!$this->siteMap->addSiteMapEntry($siteMapEntry)) {
                        throw new RouteAlreadyExistsException(
                            route: $route,
                            name: $name,
                            message: 'Route already exists',
                            code: 500,
                        );
                    }
                }
            }
        }
    }
}
