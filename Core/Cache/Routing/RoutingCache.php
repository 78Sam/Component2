<?php

declare(strict_types=1);

namespace ComponentPHP\Cache\Routing;

use ComponentPHP\Cache\AbstractCache;
use ComponentPHP\Routing\Models\Request;
use ComponentPHP\Routing\Models\SiteMap;

class RoutingCache extends AbstractCache
{
    public function readSiteMap(): ?SiteMap
    {
        return $this->readCache('SiteMap');
    }

    /**
     * @return ?Request[]
     */
    public function readRequests(): ?array
    {
        return $this->readCache('Requests');
    }

    #[\Override]
    protected function getDir(): string
    {
        return __DIR__;
    }
}
