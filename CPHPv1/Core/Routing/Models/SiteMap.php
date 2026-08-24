<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Models;

use ComponentPHP\Cache\Cacheable;

class SiteMap implements Cacheable
{
    /**
     * @param array<string, SiteMapEntry> $routes
     * @param array<string, SiteMapEntry> $names
     */
    public function __construct(
        public array $routes = [],
        public array $names = [],
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
            'routes' => $this->routes,
            'names' => $this->names,
        ];
    }

    public function addSiteMapEntry(SiteMapEntry $siteMapEntry): bool
    {
        if (\array_key_exists($siteMapEntry->route, $this->routes)) {
            return false;
        }

        if (\array_key_exists($siteMapEntry->name, $this->names)) {
            return false;
        }

        $this->routes[$siteMapEntry->route] = $siteMapEntry;
        $this->names[$siteMapEntry->name] = $siteMapEntry;

        return true;
    }

    public function findByRoute(string $route): ?SiteMapEntry
    {
        return $this->routes[$route] ?? null;
    }

    public function findByName(string $name): ?SiteMapEntry
    {
        return $this->names[$name] ?? null;
    }
}
