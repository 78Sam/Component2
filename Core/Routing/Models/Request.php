<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Models;

readonly class Request implements \Stringable
{
    /**
     * @param array<string, string> $get
     * @param array<string, string> $post
     */
    public function __construct(
        public string $scheme,
        public string $host,
        public string $route,
        public array $get,
        public array $post,
        public string $method,
        public int $time,
        public ?int $port = null,
    ) {}

    /**
     * @param array{scheme: string, host: string, route: string, get: array, post: array, method: string, time: int, port: ?int} $properties
     */
    public static function __set_state($properties): self
    {
        return new Request(...$properties);
    }

    public function __toString(): string
    {
        $hostAndPort = $this->port === null ? $this->host : "{$this->host}:{$this->port}";
        $query = $this->get === [] ? '' : '?' . http_build_query($this->get, encoding_type: PHP_QUERY_RFC3986);

        return "{$this->method}@{$this->scheme}://{$hostAndPort}{$this->route}{$query}";
    }
}
