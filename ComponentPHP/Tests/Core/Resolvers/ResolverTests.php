<?php

declare(strict_types=1);

namespace Tests\Core\Resolvers;

use Core\Testing\AbstractTest;
use Core\Testing\Attributes\Test;
use Core\Utility\Resolvers\RequestResolver;

class ResolverTests extends AbstractTest
{
    private RequestResolver $requestResolver;

    public function __construct()
    {
        $this->requestResolver = new RequestResolver(throwErrors: false);
    }

    #[Test]
    public function stringTest(): void
    {
        print_r('hi');
        // $this->assertEquals(2, "hello", "Failed");
        assert(1 > 2, "Nope");
    }
}
