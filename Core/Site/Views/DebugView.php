<?php

declare(strict_types=1);

namespace ComponentPHP\Site\Views;

use ComponentPHP\Components\AbstractTemplate;
use ComponentPHP\Components\Model\Component;

class DebugView extends AbstractTemplate
{
    protected static function draw(array $values): Component|string
    {
        return self::loadComponent(__DIR__ . '/../Components/debug.html', absolutePath: true);
    }
}
