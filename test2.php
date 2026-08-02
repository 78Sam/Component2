<?php

declare(strict_types=1);

readonly class test
{
    public function __construct(
        public string $x,
        public string $y,
    ) {
    }
}

$x = new \test('hi', 'hello');
$p = (array) $x;
print_r($p);
