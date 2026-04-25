<?php

declare(strict_types=1);

namespace ComponentPHP\Components;

use ComponentPHP\Cache\Components\ComponentCache;
use ComponentPHP\Components\Exceptions\ComponentNotFoundException;
use ComponentPHP\Components\Exceptions\FileNotFoundException;
use ComponentPHP\Components\Models\Component;
use ComponentPHP\Logging\Logger;
use ComponentPHP\Logging\Models\LoggingChannels;

abstract class AbstractTemplate
{
    private const string COMPONENTS_PATTERN = '/<component\s+!@\(\s*#component\|(?<name>\w+)\s*\)>(?<component>.*?)<\/component>/s';

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

        $path = $absolutePath ? $filename : CPHP_COMPONENTS_DIR . "/{$filename}";
        $filenameNoExtension = pathinfo($filename, PATHINFO_FILENAME);

        /** @var array<string, Component> $components */
        $components = [];
        if (!CPHP_IS_DEV) {
            $components = $this->componentCache->readComponentsFileCache($filenameNoExtension);
        }

        if (CPHP_IS_DEV || $components === []) {
            $content = file_get_contents($path);
            if ($content === false)
            {
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
        foreach ($this->components as $file => $components) {
            if (\array_key_exists($component, $components)) {
                return clone $components[$component];
            }
        }

        throw new ComponentNotFoundException("Unable to find component '{$component}'");
    }

    /**
     * @return array<string, Component>
     */
    private function buildComponents(string $content): array
    {
        $matches = [];
        if (!preg_match_all(self::COMPONENTS_PATTERN, $content, $matches))
        {
            return [];
        }
        /** @var array{name: string[], component: string[]} $matches */

        /** @var array<string, Component> $components */
        $components = [];
        for ($i = 0; $i < \count($matches['name']); $i++) {
            $name = $matches['name'][$i];
            $body = trim($matches['component'][$i]);
            $components[$matches['name'][$i]] = new Component($name, $body);
        }

        return $components;
    }
}
