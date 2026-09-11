<?php

declare(strict_types=1);

namespace Core\Components;

use Core\Components\Models\Component;

abstract class AbstractTemplate
{
    public const string VARIABLE_PATTERN = '/!@\(\s*\$(?<variable_name>[a-zA-Z_]{1}\w*)\s*\)/';
    public const string COMPONENT_PATTERN = '/!@\(\s*component\|(?<component_name>\w+)\s*\)(?<body>.*?)!@\(\s*end\s*\)/s';

    /** @var array<string, array<string, Component>> */
    private array $components = [];
    /**
     * @param string $filename Path relative to App/Components
     * 
     * @return list<string> The names of the components loaded via this file
     */
    // protected function loadFile(string $filename): array
    public function loadFile(string $filename): array
    {
        $filename = normalisePath($filename);
        $path = relativeToAbsolutePath("App/Components{$filename}");
        if (!array_key_exists($path, $this->components)) {
            $this->components[$path] = $this->parseComponents($path);
        }

        // return array_keys($this->components[$path]);
        return $this->components;
    }

    /**
     * @return array<string, Component>
     */
    private function parseComponents(string $path): array
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new \Exception("Unable to read file '{$path}'");
        }

        $componentMatches = [];
        $componentCount = preg_match_all(self::COMPONENT_PATTERN, $content, $componentMatches, flags: PREG_SET_ORDER);
        if ($componentCount === 0 || $componentCount === false) {
            return [];
        }

        $components = [];
        foreach ($componentMatches as $componentMatch) {
            $sockets = [];
            $variableMap = [];
            $componentBody = $componentMatch['body'];
            
            $variableMatches = [];
            preg_match_all(self::VARIABLE_PATTERN, $componentBody, $variableMatches, flags: PREG_SET_ORDER);
            foreach ($variableMatches as $index => $variable) {
                $split = explode($variable[0], $componentBody, limit: 2);

                $sockets["_socket_block_{$index}"] = $split[0];
                $variableName = $variable['variable_name'];
                $variablePseudonym = "_socket_variable_{$variableName}_{$index}";
                $sockets[$variablePseudonym] = '';

                if (!array_key_exists($variableName, $variableMap)) {
                    $variableMap[$variableName] = [];
                }
                $variableMap[$variableName][] = $variablePseudonym;

                $componentBody = $split[1];
            }
            $sockets["_socket_block_-1"] = $componentBody;

            $componentName = $componentMatch['component_name'];
            $components[$componentName] = new Component($componentName, $sockets, $variableMap);
        }

        return $components;
    }
}
