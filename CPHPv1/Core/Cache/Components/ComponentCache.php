<?php

declare(strict_types=1);

namespace ComponentPHP\Cache\Components;

use ComponentPHP\Cache\AbstractCache;
use ComponentPHP\Components\Models\Component;

class ComponentCache extends AbstractCache
{
    /**
     * @return array<string, Component>
     */
    public function readComponentsFileCache(string $filename): array
    {
        return $this->readCache($filename, []);
    }

    #[\Override]
    protected function getDir(): string
    {
        return __DIR__;
    }
}
