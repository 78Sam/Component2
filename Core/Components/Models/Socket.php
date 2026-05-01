<?php

declare(strict_types=1);

namespace ComponentPHP\Components\Models;

class Socket
{
	public function __construct(
		public string $name,
		public string|Component $value,
	) {
	}

	/** @param array{name: string, value: string|Component} $properties */
	public static function __set_state(array $properties): self
    {
        return new self(...$properties);
    }

	public function __toString()
	{
		return (string) $this->value;
	}
}
