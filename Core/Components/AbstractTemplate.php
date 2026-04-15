<?php

declare(strict_types=1);

namespace ComponentPHP\Components;

use ComponentPHP\Cache\CacheLine;
use ComponentPHP\Cache\Components\ComponentCache;
use ComponentPHP\Components\Exceptions\FileNotFoundException;
use ComponentPHP\Components\Models\Component;

abstract class AbstractTemplate
{
    private const string COMPONENTS_PATTERN = '/<component\s+!@\(\s*#component\|(?<name>\w+)\s*\)>(?<component>.*?)<\/component>/s';

    private ComponentCache $componentCache;

    public function __construct()
    {
        $this->componentCache = new ComponentCache();
    }

    /**
     * @throws FileNotFoundException
     * @return array<string, Component>
     */
    protected function loadFile(string $filename, bool $absolutePath = false): array
    {
        $path = $absolutePath ? $filename : CPHP_COMPONENTS_DIR . "/{$filename}";
        if (!file_exists($path))
        {
            throw new FileNotFoundException(message: "Cannot find component at '{$path}'", code: 404);
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