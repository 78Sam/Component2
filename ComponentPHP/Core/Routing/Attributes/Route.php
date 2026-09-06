<?php

declare(strict_types=1);

namespace Core\Routing\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
readonly class Route
{
    public string $route;

    /** @param list<string> $HTTPVerbs */
    public function __construct(
        string $route,
        public string $name,
        public array $HTTPVerbs = [],
    ) {
        $this->route = '/' . trim($route, '/');
    }
}
