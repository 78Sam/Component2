<?php

declare(strict_types=1);

namespace App\Controllers;

use ComponentPHP\Routing\Controllers\AbstractController;
use ComponentPHP\Routing\Attributes\Route;

class HomeController extends AbstractController
{
	#[Route(route: '/', name: 'app_home')]
	public function index(): void
	{
		$this->render('app.html');
	}

	#[Route(route: '/random-number', name: 'app_randomNumber')]
	public function randomNumber(): void
	{
		header('Content-Type: text/html;'); // Doesnt seem to be needed
		echo rand(0, 500);
	}

	#[Route(route: '/validate', name: 'app_validate')]
	public function validate(): void
	{
		$username = $_POST['username'];
		if (\strlen($username) > 5)
		{
			echo "Your username '{$username}' is long enough :)";
		}
		else
		{
			echo "Your username '{$username}' is NOT long enough :(";
		}
	}

	#[Route(route: '/test/url', name: 'app_testUrl')]
	public function testUrl(): void
	{
		echo $this->router->getUrlFor('app_sam');
	}

	#[Route(route: '/uma', name: 'app_uma')]
	public function uma(): void
	{
		echo 'I love uma <3';
	}
}
