<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Exceptions;

use Throwable;

class PageNotFoundException extends \Exception
{
	public readonly string $route;
	public function __construct(string $route, string $message = "", int $code = 0, Throwable|null $previous = null)
	{
		$this->route = $route;

		return parent::__construct(message: $message, code:  $code, previous: $previous);
	}
}
