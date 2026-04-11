<?php

declare(strict_types=1);

namespace ComponentPHP\Components;

use ComponentPHP\Cache\CacheLine;
use ComponentPHP\Cache\Components\ComponentCache;
use ComponentPHP\Components\Exceptions\ComponentNotFoundException;
use ComponentPHP\Components\Models\Component;

abstract class AbstractTemplate
{
    // public static function render(array $values = []): string
    // {
    //     $schematic = static::draw($values);

    //     return \is_string($schematic) ? $schematic : $schematic->render();
    // }

    // abstract protected static function draw(array $values): Component|string;

    // /**
    //  * @throws ComponentNotFoundException
    //  */
    // protected static function loadComponent(string $name, bool $absolutePath = false): Component
    // {
    //     $path = $absolutePath ? $name : CPHP_COMPONENTS_DIR . "/{$name}";
    //     if (!file_exists($path))
    //     {
    //         throw new ComponentNotFoundException(message: "Cannot find component at '{$path}'", code: 404);
    //     }

    //     $content = '';
    //     $cachedComponents = ComponentCache::readCache(CacheLine::Components, []);
    //     if (!CPHP_IS_DEV && \array_key_exists($path, $cachedComponents))
    //     {
    //         $content = $cachedComponents[$path];
    //         cphpLog("Found cached value for component '{$path}'");
    //     }
    //     else
    //     {
    //         $content = file_get_contents($path);
    //         $cachedComponents[$path] = $content;
    //         ComponentCache::writeCache(CacheLine::Components, $cachedComponents);
    //     }

    //     return new Component(
    //         path: $path,
    //         content: $content,
    //     );
    // }

    private const string COMPONENTS_PATTERN = '/<component\s+!@\(\s*#component\|(?<name>\w+)\s*\)>(?<component>.*?)<\/component>/s';
    private const string VARIABLE_PATTERN = '/!@\(\s*\$(?<name>[a-zA-Z_]+\w*)\s*\)/';

    private ComponentCache $componentCache;

    public function __construct()
    {
        $this->componentCache = new ComponentCache();
    }

    /**
     * @throws ComponentNotFoundException
     * @return array<string, Component>
     */
    protected function loadFile(string $filename, bool $absolutePath = false): array
    {
        $path = $absolutePath ? $filename : CPHP_COMPONENTS_DIR . "/{$filename}";
        if (!file_exists($path))
        {
            throw new ComponentNotFoundException(message: "Cannot find component at '{$path}'", code: 404);
        }

        /** @var array<string, Component> $components */
        $components = [];
        if (!CPHP_IS_DEV)
        {
            $components = $this->componentCache->readCache(CacheLine::Components, []);
        }

        if (CPHP_IS_DEV || $components === [])
        {
            $components = $this->buildComponents(file_get_contents($path));
            $this->componentCache->writeCache(CacheLine::Components, $components);
        }

        return $components;
    }

    /**
     * @return array<string, Component>
     */
    private function buildComponents(string $content): array
    {
        /** @var array<string, Component> $components */
        $components = [];
        $matches = [];
        preg_match_all(self::COMPONENTS_PATTERN, $content, $matches);
        for ($i = 0; $i < \count($matches['name']); $i++)
        {
            $name = $matches['name'][$i];
            $body = trim($matches['component'][$i]);
            $components[$matches['name'][$i]] = new Component($name, $body);
        }

        return $components;
    }
}