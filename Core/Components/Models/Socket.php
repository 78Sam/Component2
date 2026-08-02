<?php

declare(strict_types=1);

namespace ComponentPHP\Components\Models;

use ComponentPHP\Cache\Cacheable;

class Socket implements Cacheable
{
    public function __construct(
        public string $name,
        public string|Component $value,
    ) {}

    public function __clone()
    {
        $this->value = $this->value instanceof Component ? clone $this->value : $this->value;
    }

    #[\Override]
    public static function in(array $properties): self
    {
        return new self(...$properties);
    }

    #[\Override]
    public function out(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
        ];
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}
