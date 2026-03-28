<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Model;

class Response
{
	public function __construct(
		public readonly string $content,
		public readonly int $responseCode = 200, // TODO: This could be an enum for the response codes
	) {
	}
}
