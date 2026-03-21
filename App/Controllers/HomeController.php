<?php

declare(strict_types=1);

namespace App\Controllers;

use ComponentPHP\Routing\AbstractController;

class HomeController extends AbstractController
{
	public function index(): void
	{
		$x = 'woah this is a really long line that someone should probably cut down on quite significantly as its really hard to read';
	}
}
