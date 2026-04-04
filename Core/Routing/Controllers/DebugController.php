<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Controllers;

use ComponentPHP\Cache\CacheLine;
use ComponentPHP\Cache\Routing\RoutingCache;
use ComponentPHP\Routing\Attributes\Route;
use ComponentPHP\Routing\Model\Request;
use ComponentPHP\Routing\Model\Response;

class DebugController extends AbstractController
{
    #[Route(route: '/_debug', name: 'debug_home')]
    public function home(): Response
    {
        $cache = new RoutingCache();

        /** @var Request[] $recentRequests */
        $recentRequests = $cache->readCache(CacheLine::Requests, default: []); // TODO: Reading the cache clears it??...

        ob_start();
        foreach ($recentRequests as $request)
        {
            var_dump($request);
        }

        $content = ob_get_clean();

        return new Response($content);
    }

	#[Route(route: '/_debug/phpinfo', name: 'debug_phpinfo')]
	public function phpInfo()
	{
		ob_start();
		phpinfo();
		$phpInfo = ob_get_clean();

		return new Response($phpInfo);
	}
}
