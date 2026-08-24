<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Models;

class Request
{
    public function __construct(
        public readonly string $host,
        public readonly string $scheme,
        public readonly string $path,
        public readonly int $port,
        public readonly string $queryString,
        public readonly string $method,
        public readonly int $requestTime,
        public readonly int $serverTime,
        public readonly array $get = [],
        public readonly array $post = [],
        public readonly array $files = [],
        public readonly array $cookies = [],
    ) {
    }
}
