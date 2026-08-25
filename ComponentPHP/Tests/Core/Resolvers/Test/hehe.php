<?php

declare(strict_types=1);

namespace Tests\Core\Resolvers\Test;

use Core\Testing\AbstractTest;
use Core\Testing\Attributes\Test;

class hehe extends AbstractTest
{
    #[Test]
    public function sayhi()
    {
        print_r("hi!");
    }
}
