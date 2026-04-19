<?php

declare(strict_types=1);

namespace ComponentPHP\Logging\Models;

enum LoggingChannels: string
{
    case Cache = 'Cache';
    case Core = 'Core';
    case Router = 'Router';
    case Templating = 'Templating';
}
