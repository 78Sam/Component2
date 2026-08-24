<?php

declare(strict_types=1);

namespace ComponentPHP\Cache;

interface Cacheable
{
    public static function in(array $properties): self;

    public function out(): array;
}
