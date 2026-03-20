<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Model;

readonly class Request
{
	private array $queryParameters;

	public function __construct(
		public ?string $scheme = null,
		public ?string $host = null,
		public ?string $path = null,
		public ?string $query = null,
		public ?int $port = null,
	) {
		$this->queryParameters = $this->parseQueryParameters();
	}

	public function get(string $name, mixed $default = null): mixed
	{
		return $this->queryParameters[$name] ?? $default;
	}

	private function parseQueryParameters(): array
	{
		if ($this->query === null)
		{
			return [];
		}

		$queryParameters = [];
		foreach (explode('&', $this->query) as $queryParameter) {
			$keyValuePair = explode('=', $queryParameter);
			$queryParameters[$keyValuePair[0]] = $keyValuePair[1];
		}

		return $queryParameters;
	}
}
