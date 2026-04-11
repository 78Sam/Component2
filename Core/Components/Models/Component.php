<?php

declare(strict_types=1);

namespace ComponentPHP\Components\Models;

class Component
{
    private const string VARIABLE_PATTERN = '/!@\(\s*\$(?<name>[a-zA-Z_]+\w*)\s*\)/';

    /** @var array<string, bool> $sockets */
    private array $sockets = [];

    public function __construct(
        public readonly string $name,
        public string $body,
        ?array $sockets = null,
    ) {
        $this->sockets = $sockets ?? $this->findSockets();
    }

    public static function __set_state(array $properties): self
    {
        return new Component(...$properties);
    }

    // public function fill(string $name, string $value): self
    // {

    // }

    /**
     * @return array<string, bool>
     */
    private function findSockets(): array
    {
        $matches = [];
        preg_replace_callback(self::VARIABLE_PATTERN, function ($value) use (&$matches) {
            $matches[$value['name']] = true;

            return "!@(\${$value["name"]})";
        }, $this->body);

        return $matches;
    }
}
