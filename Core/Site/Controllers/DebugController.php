<?php

declare(strict_types=1);

namespace ComponentPHP\Site\Controllers;

use ComponentPHP\Cache\CacheLine;
use ComponentPHP\Cache\Routing\RoutingCache;
use ComponentPHP\Routing\Attributes\Route;
use ComponentPHP\Routing\Controllers\AbstractController;
use ComponentPHP\Routing\Models\Request;
use ComponentPHP\Routing\Models\Response;
use ComponentPHP\Site\Views\DebugView;

class DebugController extends AbstractController
{
    #[Route(route: '/_debug', name: 'debug_home')]
    public function home(): Response
    {
        // /** @var Request[] $recentRequests */
        // $recentRequests = RoutingCache::readCache(CacheLine::Requests, default: []);

        // ob_start();
        // foreach ($recentRequests as $request)
        // {
        //     var_dump($request);
        // }

        // $content = ob_get_clean();

        // return new Response($content);
        return new Response(DebugView::render());
    }

    #[Route(route: '/_debug/requests', name: 'debug_requests')]
    public function getRequests(): Response
    {
        return new Response('requests');
    }

    #[Route(route: '/_debug/site-map', name: 'debug_siteMap')]
    public function getSiteMap(): Response
    {
        return new Response('site-map');
    }

    #[Route(route: '/_debug/php-info', name: 'debug_phpInfo')]
    public function getPHPInfo(): Response
    {
        return new Response('phpinfo');
    }

	// #[Route(route: '/_debug/phpinfo', name: 'debug_phpinfo')]
	// public function phpInfo()
	// {
	// 	ob_start();
	// 	phpinfo();
	// 	$phpInfo = ob_get_clean();

	// 	return new Response($phpInfo);
	// }
}
