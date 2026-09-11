<?php

declare(strict_types=1);

namespace Core\Routing\Models;

class Response
{
    public function __construct(
        public readonly string $content,
        public readonly int $responseCode = 200, // TODO(Sam): This could be an enum for the response codes
        public readonly string $contentType = 'text/html',
        public readonly string $charset = 'utf-8',
    ) {}
}
