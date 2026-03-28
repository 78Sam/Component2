<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Controllers;

use ComponentPHP\Routing\Attributes\Route;

class DebugController extends AbstractController
{
	#[Route(route: '/_debug/phpinfo', name: 'debug_phpinfo')]
	public function _phpInfo()
	{
		phpinfo();
	}
}
