<?php

class Test
{
    private int $x = 3;

    public function getX()
    {
        return $this->x;
    }
}

class Test2 extends Test
{
}

$p = new Test2();
echo $p->getX();