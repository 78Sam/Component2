<?php

declare(strict_types=1);

class Tester
{
    public function sayhi()
    {
        return 'hi';
    }
}


$reflection = new \ReflectionClass(Tester::class);

$x = new ($reflection->name)();
print_r($x);
$meth = 'sayhi';
print_r($x->$meth());