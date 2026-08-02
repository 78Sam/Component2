<?php

declare(strict_types=1);

namespace ComponentPHP\Logging\Models;

enum LoggingChannel: string
{
    case Cache = 'Cache';
    case Core = 'Core';
    case Router = 'Router';
    case Templating = 'Templating';
}
