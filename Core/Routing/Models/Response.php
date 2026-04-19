<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Models;

use ComponentPHP\Components\Models\Component;

class Response
{
    public function __construct(
        public readonly string|Component $content,
        public readonly int $responseCode = 200, // TODO: This could be an enum for the response codes
    ) {}
}
