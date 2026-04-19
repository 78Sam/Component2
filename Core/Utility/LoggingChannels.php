<?php

declare(strict_types=1);

namespace ComponentPHP\Logging\Channels;

enum LoggingChannels: string
{
    case Core = 'Core';
    case Templating = 'Templating';
    case Router = 'Router';
}
