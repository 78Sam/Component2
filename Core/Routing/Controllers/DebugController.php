<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Controllers;

use ComponentPHP\Routing\Attributes\Route;
use ComponentPHP\Routing\Model\Response;

class DebugController extends AbstractController
{
	#[Route(route: '/_debug/phpinfo', name: 'debug_phpinfo')]
	public function _phpInfo()
	{
		ob_start();
		phpinfo();
		$phpInfo = ob_get_clean();

		return new Response($phpInfo);
	}
}
