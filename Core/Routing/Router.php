<?php

declare(strict_types=1);

namespace ComponentPHP\Routing;

use ComponentPHP\Routing\Exceptions\HostNotFoundException;
use ComponentPHP\Routing\Exceptions\UriNotFoundException;
use ComponentPHP\Routing\Exceptions\UrlParseException;
use ComponentPHP\Routing\Model\Request;

class Router
{
	private Request $request;

	/**
	 * @throws UrlParseException
	 * @throws HostNotFoundException
	 * @throws UriNotFoundException
	 */
	public function __construct()
	{
		$url = $this->getUrl();
		$urlComponents = parse_url($url);
		if (!\is_array($urlComponents))
		{
			$type = gettype($urlComponents);

			throw new UrlParseException("Unable to parse url '{$url}', got type {$type}");
		}

		if (\array_key_exists('user', $urlComponents))
		{
			unset($urlComponents['user']);
		}

		if (\array_key_exists('pass', $urlComponents))
		{
			unset($urlComponents['pass']);
		}

		if (\array_key_exists('fragment', $urlComponents))
		{
			unset($urlComponents['fragment']);
		}

		$this->request = new Request(...$urlComponents);
	}

	/**
	 * @throws HostNotFoundException
	 * @throws UriNotFoundException
	 */
	private function getUrl(): string
	{
		$protocol = ($_SERVER['HTTPS'] ?? 'off') === 'on' ? 'https' : 'http';

		$host = $_SERVER['HTTP_HOST'] ?? null;
		if ($host === null)
		{
			throw new HostNotFoundException("Unable to parse host");
		}

		$uri = $_SERVER['REQUEST_URI'] ?? null;
		if ($uri === null)
		{
			throw new UriNotFoundException("Unable to parse uri");
		}

		return "{$protocol}://{$host}{$uri}";
	}

	public function getCoreRequest(): Request
	{
		return $this->request;
	}
}
