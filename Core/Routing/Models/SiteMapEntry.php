<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Models;

use ComponentPHP\Routing\Controllers\AbstractController;

readonly class SiteMapEntry
{
    /**
     * @param class-string<AbstractController> $class
     * @param list<string> $HTTPVerbs
     */
    public function __construct(
        public string $route,
        public string $name,
        public string $class,
        public string $method,
        public array $HTTPVerbs = [],
    ) {}

    /**
     * @param array{route: string, name: string, class: string, method: string, HTTPVerbs: array} $properties
     */
    public static function __set_state($properties): self
    {
        return new SiteMapEntry(...$properties);
    }
}
