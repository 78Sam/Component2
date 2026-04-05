<?php

declare(strict_types=1);

namespace ComponentPHP\Components;

use ComponentPHP\Cache\CacheLine;
use ComponentPHP\Cache\Components\ComponentCache;
use ComponentPHP\Components\Exceptions\ComponentNotFoundException;
use ComponentPHP\Components\Model\Component;

abstract class AbstractTemplate
{
    public static function render(array $values = []): string
    {
        $schematic = static::draw($values);
        if (\is_string($schematic))
        {
            return $schematic;
        }

        return $schematic->render();
    }

    abstract protected static function draw(array $values): Component|string;

    /**
     * @throws ComponentNotFoundException
     */
    protected static function loadComponent(string $name): Component
    {
        $path = CPHP_COMPONENTS_DIR . "/{$name}";
        if (!file_exists($path))
        {
            throw new ComponentNotFoundException(message: "Cannot find component at '{$path}'", code: 404);
        }

        $content = '';
        $cachedComponents = ComponentCache::readCache(CacheLine::Components, []);
        if (\array_key_exists($path, $cachedComponents))
        {
            $content = $cachedComponents[$path];
            cphpLog("Found cached value for component '{$path}'");
        }
        else
        {
            $content = file_get_contents($path);
            $cachedComponents[$path] = $content;
            ComponentCache::writeCache(CacheLine::Components, $cachedComponents);
        }

        return new Component(
            path: $path,
            content: $content,
        );
    }
}
