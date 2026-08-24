<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Models;

use ComponentPHP\Cache\Cacheable;

readonly class Request implements \Stringable, Cacheable
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

    #[\Override]
    public static function in(array $properties): self
    {
        return new self(...$properties);
    }

    #[\Override]
    public function out(): array
    {
        return [
            'scheme' => $this->scheme,
            'host' => $this->host,
            'route' => $this->route,
            'get' => $this->get,
            'post' => $this->post,
            'method' => $this->method,
            'time' => $this->time,
        ];
    }

    public function __toString(): string
    {
        $hostAndPort = $this->port === null ? $this->host : "{$this->host}:{$this->port}";
        $query = $this->get === [] ? '' : '?' . http_build_query($this->get, encoding_type: PHP_QUERY_RFC3986);

        return "{$this->method}@{$this->scheme}://{$hostAndPort}{$this->route}{$query}";
    }
}
