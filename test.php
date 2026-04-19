<?php

class X
{
    public function __construct(
        public int $p,
    ) {
    }
}

$p = [
    'hi' => X::__set_state(['p' => 12]),
];

print_r($p);