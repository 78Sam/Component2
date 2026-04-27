<?php

declare(strict_types=1);

namespace ComponentPHP\Components;

use ComponentPHP\Cache\Components\ComponentCache;
use ComponentPHP\Components\Exceptions\ComponentNotFoundException;
use ComponentPHP\Components\Exceptions\FileNotFoundException;
use ComponentPHP\Components\Models\Component;
use ComponentPHP\Logging\Logger;
use ComponentPHP\Logging\Models\LoggingChannels;
use ComponentPHP\Utility\Config;

abstract class AbstractTemplate
{
    private const string COMPONENTS_PATTERN = '/!@\(\s*component\|(?<name>\w+)\s*\)(?<component>.*?)!@\(\s*end\s*\)/s';

    protected Logger $logger;

    /** @var array<string, array<string, Component>> $components */
    private array $components = [];
    private ComponentCache $componentCache;

    public function __construct()
    {
        $this->logger = new Logger(LoggingChannels::Templating);
        $this->componentCache = new ComponentCache();
        $this->loadFiles();
    }

    abstract protected function loadFiles(): void;

    /**
     * @throws FileNotFoundException
     */
    protected function loadFile(string $filename, bool $absolutePath = false): void
    {
        if (\array_key_exists($filename, $this->components)) {
            return;
        }

        $path = $absolutePath ? $filename : Config::COMPONENTS_DIR . "/{$filename}";
        $filenameNoExtension = pathinfo($filename, PATHINFO_FILENAME);

        /** @var array<string, Component> $components */
        $components = [];
        if (Config::IS_PROD) {
            $components = $this->componentCache->readComponentsFileCache($filenameNoExtension);
        }

        // If we are on DEV, or we didn't get any components from our cache, try and build them
        if (Config::IS_DEV || $components === []) {
            $content = \file_get_contents($path);
            if ($content === false) {
                throw new FileNotFoundException(message: "Cannot find component at '{$path}'", code: 404);
            }

            $components = $this->buildComponents($content);
            $this->componentCache->writeCache($filenameNoExtension, $components);
        }

        $this->components[$filename] = $components;
    }

    /**
     * Get a clone of the specified component
     *
     * @throws ComponentNotFoundException
     */
    public function get(string $component): Component
    {
        foreach (\array_values($this->components) as $components) {
            if (\array_key_exists($component, $components)) {
                return clone $components[$component];
            }
        }

        throw new ComponentNotFoundException("Unable to find component '{$component}'");
    }

    /**
     * @param list<array<string, string|Component>> $values
     *
     * @return Component[]
     */
    public function stack(string $componentName, array $values): array
    {
        $components = [];
        foreach ($values as $row) {
            $component = $this->get($componentName);
            foreach ($row as $key => $value) {
                $component->fill($key, $value);
            }
            $components[] = $component;
        }

        return $components;
    }

    public function collect(array $items, string $separator = '')
    {
        $sockets = [];
        $count = 0;
        foreach ($items as $item) {
            $sockets["_chunk_{$count}"] = $item;
            $count++;
            $sockets["_chunk_{$count}"] = $separator;
            $count++;
        }
        array_pop($sockets);

        return new Component('_collector', '', $sockets);
    }

    /**
     * @return array<string, Component>
     */
    private function buildComponents(string $content): array
    {
        $matches = [];
        if (!preg_match_all(self::COMPONENTS_PATTERN, $content, $matches)) {
            return [];
        }
        /** @var array{name: string[], component: string[]} $matches */

        $components = [];
        for ($matchNumber = 0; $matchNumber < \count($matches['name']); $matchNumber++) {
            $name = $matches['name'][$matchNumber];
            $body = \trim($matches['component'][$matchNumber]);

            $components[$name] = new Component($name, $body);
        }

        return $components;
    }
}
