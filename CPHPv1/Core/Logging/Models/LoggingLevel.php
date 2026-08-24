<?php

declare(strict_types=1);

namespace ComponentPHP\Logging\Models;

enum LoggingLevel: string
{
    case Critical = 'Critical';
    case Error = 'Error';
    case Warning = 'Warning';
    case Info = 'Info';
    case Debug = 'Debug';
}
