<?php

class Test
{
    public static array $x = [];

    public static function add(int $p)
    {
        self::$x[] = $p;
    }
}

Test::add(2);
Test::add(4);
Test::add(3);

print_r(Test::$x);