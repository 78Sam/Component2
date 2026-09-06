<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Request;
use Core\Routing\Models\SiteMapEntry;
use Core\Utility\ClassFinder;
use Core\Utility\Validators\Services\ValidatorService;
use Core\Utility\Validators\Types\IntOrStringIntValidator;
use Core\Utility\Validators\Types\StringValidator;

final class Router
{
    private ClassFinder $classFinder;

    /** @var array<string, SiteMapEntry> */
    public array $siteMapEntries = [];

    public function __construct()
    {
        $this->classFinder = new ClassFinder();
        $this->createSiteMap();
    }

    public function buildRequest(array $server, array $get, array $post, array $files, array $cookies): Request
    {
        $requirements = [
            'SERVER_NAME' => new StringValidator('SERVER_NAME'),
            'REQUEST_SCHEME' => new StringValidator('REQUEST_SCHEME'),
            'HTTPS' => new StringValidator('HTTPS'),
            'REQUEST_URI' => new StringValidator('REQUEST_URI'),
            'SERVER_PORT' => new IntOrStringIntValidator('SERVER_PORT'),
            'QUERY_STRING' => new StringValidator('QUERY_STRING'),
            'REQUEST_METHOD' => new StringValidator('REQUEST_METHOD'),
            'REQUEST_TIME' => new IntOrStringIntValidator('REQUEST_TIME'),
        ];

        ValidatorService::validate($requirements, $server);

        $httpsScheme = in_array($requirements['HTTPS']->getValueWithDefault(''), ['', 'off'], true) ? 'HTTP' : 'HTTPS';

        return new Request(
            host: $requirements['SERVER_NAME']->getValue(),
            scheme: strtoupper($requirements['REQUEST_SCHEME']->getValueWithDefault($httpsScheme)),
            path: parse_url($requirements['REQUEST_URI']->getValue(), PHP_URL_PATH),
            port: $requirements['SERVER_PORT']->getValue(),
            queryString: $requirements['QUERY_STRING']->getValue(),
            method: $requirements['REQUEST_METHOD']->getValue(),
            requestTime: $requirements['REQUEST_TIME']->getValue(),
            serverTime: time(),
            get: $get,
            post: $post,
            files: $files,
            cookies: $cookies,
        );
    }

    public function createSiteMap(): void
    {
        $controllers = $this->classFinder->byExtension('App/Controllers', AbstractController::class);
        foreach ($controllers as $controller) {
            foreach ($controller->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $routeAttributes = $method->getAttributes(Route::class);
                $numRouteAttributes = count($routeAttributes);
                if ($numRouteAttributes === 0) {
                    continue;
                }

                if ($numRouteAttributes > 1) {
                    throw new \LogicException(
                        "Controller methods should only have 1 route attribute, '{$method->name}' has {$numRouteAttributes}",
                    );
                }

                $routeAttribute = $routeAttributes[0]->newInstance();
                $route = $routeAttribute->route;
                if (array_key_exists($route, $this->siteMapEntries)) {
                    throw new \LogicException("Route already registered '{$route}'");
                }

                $this->siteMapEntries[$route] = new SiteMapEntry($routeAttribute, $method);
            }
        }
    }
}
