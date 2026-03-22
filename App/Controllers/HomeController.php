<?php

declare(strict_types=1);

namespace App\Controllers;

use ComponentPHP\Routing\AbstractController;
use ComponentPHP\Routing\Attributes\Route;

class HomeController extends AbstractController
{
	#[Route(route: '/', name: 'app_home')]
	public function index(): void
	{
		echo $this->router->getUrlFor('app_sam');
	}
}
