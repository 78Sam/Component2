<?php

declare(strict_types=1);

namespace App\Controllers\Afdbm;

use ComponentPHP\Routing\AbstractController;
use ComponentPHP\Routing\Model\Route;

final class SamController extends AbstractController
{
	#[Route('/home/sam', name: 'app_samhome')]
	public function test()
	{
		dump("Hello");
	}
}
