<?php

declare(strict_types=1);

namespace ComponentPHP\Site\Controllers;

use ComponentPHP\Cache\Routing\RoutingCache;
use ComponentPHP\Routing\Attributes\Route;
use ComponentPHP\Routing\Controllers\AbstractController;
use ComponentPHP\Routing\Models\Response;
use ComponentPHP\Routing\Models\SiteMap;
use ComponentPHP\Site\Views\DebugView;

final class DebugController extends AbstractController
{
    #[Route(route: '/_debug', name: 'debug_home')]
    public function home(): Response
    {
        $routingCache = new RoutingCache();

        return new Response(new DebugView()->mainPage($routingCache->readCache('Requests', [])));
    }

    #[Route(route: '/_debug/requests', name: 'debug_requests')]
    public function getRequests(): Response
    {
        $routingCache = new RoutingCache();

        return new Response(new DebugView()->requests($routingCache->readCache('Requests', [])));
    }

    #[Route(route: '/_debug/site-map', name: 'debug_siteMap')]
    public function getSiteMap(): Response
    {
        $routingCache = new RoutingCache();

        /** @var ?SiteMap $siteMap */
        $siteMap = $routingCache->readCache('SiteMap', null);
        $siteMapEntries = $siteMap?->routes;

        return new Response(new DebugView()->siteMap($siteMapEntries ?? []));
    }

    #[Route(route: '/_debug/php-info', name: 'debug_phpInfo')]
    public function getPHPInfo(): Response
    {
        return new Response(new DebugView()->phpinfo());
    }
}
