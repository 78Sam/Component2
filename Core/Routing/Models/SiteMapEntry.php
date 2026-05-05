<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Models;

use ComponentPHP\Cache\Cacheable;

readonly class SiteMapEntry implements Cacheable
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

    #[\Override]
    public static function in(array $properties): self
    {
        return new self(...$properties);
    }

    #[\Override]
    public function out(): array
    {
        return [
            'route' => $this->route,
            'name' => $this->name,
            'class' => $this->class,
            'method' => $this->method,
            'HTTPVerbs' => $this->HTTPVerbs,
        ];
    }
}
