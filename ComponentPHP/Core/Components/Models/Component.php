<?php

declare(strict_types=1);

namespace Core\Components\Models;

class Component
{
    public function __construct(
        public readonly string $name,
        public readonly array $sockets,
        public readonly array $variableMap,
    ) {
    }
}
