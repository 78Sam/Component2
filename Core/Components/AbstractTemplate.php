<?php

declare(strict_types=1);

namespace ComponentPHP\Components;

use ComponentPHP\Cache\Components\ComponentCache;
use ComponentPHP\Components\Exceptions\ComponentNotFoundException;
use ComponentPHP\Components\Exceptions\FileNotFoundException;
use ComponentPHP\Components\Models\Component;
use ComponentPHP\Components\Models\Socket;
use ComponentPHP\Logging\Logger;
use ComponentPHP\Logging\Models\LoggingChannels;
use ComponentPHP\Utility\Config;

abstract class AbstractTemplate
{
    private const string COMPONENTS_PATTERN = '/!@\(\s*component\|(?<name>\w+)\s*\)(?<component>.*?)!@\(\s*end\s*\)/s';
    private const string VARIABLE_PATTERN = '/!@\(\s*\$(?<name>[a-zA-Z_]+\w*)\s*\)/';

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
     * Load a file containing components into the current context
     *
     * @throws FileNotFoundException
     *
     * @return list<string> A list of component names that have been loaded
     */
    protected function loadFile(string $filename, bool $absolutePath = false): array
    {
        $existingComponents = $this->components[$filename] ?? null;
        if ($existingComponents !== null) {
            return \array_keys($existingComponents);
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

        return \array_keys($components);
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
     * @return list<Component>
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

    /**
     * @param list<string|Component> $items
     */
    public function collect(array $items, string $separator = ''): Component
    {
        $sockets = [];
        $socketPseudonyms = [];
        $count = 0;
        foreach ($items as $item) {
            $chunk = "_chunk_{$count}";
            $sockets[$chunk] = $item;
            $socketPseudonyms[$chunk] = [$chunk];
            $count++;

            $chunk = "_chunk_{$count}";
            $sockets["_chunk_{$count}"] = $separator;
            $socketPseudonyms[$chunk] = [$chunk];
            $count++;
        }
        array_pop($sockets);

        return new Component('_collector', $sockets, $socketPseudonyms);
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

            $sockets = $this->computeSockets($body);
            $components[$name] = new Component($name, $sockets['sockets'], $sockets['pseudonyms']);
        }

        return $components;
    }

    /**
     * @return array{sockets: array<string, Socket>, pseudonyms: array<string, list<string>>}
     */
    private function computeSockets(string $componentBody): array
    {
        /** @var array<string, array{count: int, pseudonyms: list<string>}> $socketPseudonyms */
        $socketPseudonyms = [];
        /** @var list<string> $splitOrdering */
        $splitOrdering = [];
        $componentBody = preg_replace_callback(
            self::VARIABLE_PATTERN,
            function ($match) use (&$socketPseudonyms, &$splitOrdering) {
                $name = $match['name'];
                if (!array_key_exists($name, $socketPseudonyms)) {
                    $socketPseudonyms[$name] = ['count' => 0, 'pseudonyms' => []];
                }
                $count = $socketPseudonyms[$name]['count'];
                $replacement = "_chunk_variable_{$name}_{$count}";

                $socketPseudonyms[$name]['count']++;
                $socketPseudonyms[$name]['pseudonyms'][] = $replacement;
                $splitOrdering[] = $replacement;

                return $replacement;
            },
            $componentBody,
        );

        foreach ($socketPseudonyms as &$value) {
            $value['count'] = 0;
        }

        $sockets = [];
        $count = 0;
        foreach ($splitOrdering as $variableName) {
            $split = explode($variableName, $componentBody);
            $chunkName = "_chunk_{$count}";
            $sockets[$chunkName] = new Socket($chunkName, $split[0]);
            $sockets[$variableName] = new Socket($variableName, '');
            $count++;
            $componentBody = $split[1] ?? '';
        }
        $chunkName = '_chunk_-1';
        $sockets[$chunkName] = new Socket($chunkName, $componentBody);

        return [
            'sockets' => $sockets,
            'pseudonyms' => array_map(fn($socket) => $socket = $socket['pseudonyms'], $socketPseudonyms),
        ];
    }
}
