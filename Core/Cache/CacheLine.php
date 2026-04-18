<?php

declare(strict_types=1);

namespace ComponentPHP\Cache;

enum CacheLine: string
{
    // ! Routing

    case SiteMap = 'SiteMapCacheLine.php';
    case Requests = 'RequestsLine.php';

    // ! Components

    case Components = 'Components.php';

    public function path(string $path): string
    {
        return "{$path}/Lines/{$this->value}";
    }
}
