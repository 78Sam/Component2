<?php

declare(strict_types=1);

namespace ComponentPHP\Database\Enums;

enum ConnectionType: string
{
    case SQLITE3 = 'sqlite';
}
