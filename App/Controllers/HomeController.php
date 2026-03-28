<?php

declare(strict_types=1);

namespace App\Controllers;

use ComponentPHP\Routing\Controllers\AbstractController;
use ComponentPHP\Routing\Attributes\Route;
use ComponentPHP\Routing\Model\Response;

class HomeController extends AbstractController
{
	#[Route(route: '/', name: 'app_home')]
	public function index(): Response
	{
		return $this->render('app.html');
	}

	#[Route(route: '/random-number', name: 'app_randomNumber')]
	public function randomNumber(): Response
	{
		return new Response((string) rand(0, 500));
	}

	#[Route(route: '/validate', name: 'app_validate')]
	public function validate(): Response
	{
		$username = $_POST['username'];
		if (\strlen($username) > 5)
		{
			$response = "Your username '{$username}' is long enough :)";
		}
		else
		{
			$response = "Your username '{$username}' is NOT long enough :(";
		}

		return new Response($response);
	}

	#[Route(route: '/test/url', name: 'app_testUrl')]
	public function testUrl(): Response
	{
		$url = $this->router->getUrlFor('app_sam');

		return new Response($url);
	}

	#[Route(route: '/uma', name: 'app_uma')]
	public function uma(): Response
	{
		return new Response('I love uma <3', responseCode: 200);
	}
}
