<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Models;

// TODO: Add $_POST parameters to the request object
readonly class Request
{
    private array $queryParameters;

    public function __construct(
        public string $scheme,
        public string $host,
        public string $route,
        public int $time,
        public ?string $query = null,
        public ?int $port = null,
    ) {
        $this->queryParameters = $this->parseQueryParameters();
    }

    public static function __set_state($properties): Request
    {
        unset($properties['queryParameters']);

        return new Request(...$properties);
    }

    public function __toString(): string
    {
        $url = "<scheme: {$this->scheme}>://<host: {$this->host}>";
        if ($this->port) {
            $url .= "<port: {$this->port}>";
        }

        $url .= "<route: {$this->route}>";

        if ($this->query) {
            $url .= "<query: {$this->query}>";
        }

        return "Request: {$url}";
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->queryParameters[$name] ?? $default;
    }

    /**
     * @return array<string, string>
     */
    private function parseQueryParameters(): array
    {
        if ($this->query === null) {
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
