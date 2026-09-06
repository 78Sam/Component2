<?php

declare(strict_types=1);

namespace Core\Testing\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
final readonly class Test
{
    public function __construct(
        public string $description = '',
    ) {}
}
