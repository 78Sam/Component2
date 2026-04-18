<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
readonly class Route
{
    public string $route;

    public function __construct(
        string $route,
        public string $name,
    ) {
        $route = trim($route, '/');

        $this->route = "/{$route}";
    }
}
