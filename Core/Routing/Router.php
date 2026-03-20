<?php

declare(strict_types=1);

namespace ComponentPHP\Routing;

use ComponentPHP\Routing\Model\Request;

class Router
{
	private Request $request;

	public function __construct()
	{
		$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
		$url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		echo $url . '<br>';
		$this->request = new Request(...parse_url($url));
	}

	public function getCoreRequest(): Request
	{
		return $this->request;
	}
}
