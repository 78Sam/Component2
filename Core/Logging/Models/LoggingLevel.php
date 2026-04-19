<?php

declare(strict_types=1);

namespace ComponentPHP\Logging\Models;

enum LoggingLevel: string
{
    case Info = 'Info';
    case Warning = 'Warning';
    case Error = 'Error';
    case Critical = 'Critical';
}
