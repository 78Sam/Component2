<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Models;

class Response
{
    public function __construct(
        public readonly string $content,
        public readonly int $responseCode = 200,
    ) {
    }
}
