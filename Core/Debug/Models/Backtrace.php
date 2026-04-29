<?php

declare(strict_types=1);

namespace ComponentPHP\Debug\Models;

readonly class Backtrace implements \Stringable
{
    public function __construct(
        public ?string $file,
        public ?int $line,
    ) {}

    public function __toString(): string
    {
        $file = $this->file ?? 'Unknown file';
        $line = $this->line ?? 'Unknown line';

        return "{$file}::{$line}";
    }
}
