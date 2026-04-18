<?php

declare(strict_types=1);

namespace ComponentPHP\Cache\Routing;

use ComponentPHP\Cache\AbstractCache;

class RoutingCache extends AbstractCache
{
    protected function getDir(): string
    {
        return __DIR__;
    }
}
