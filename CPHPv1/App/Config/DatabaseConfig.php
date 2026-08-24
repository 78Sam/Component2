<?php

declare(strict_types=1);

namespace App\Config;

use ComponentPHP\Database\Enums\ConnectionType;

class DatabaseConfig
{
    public const ConnectionType CONNECTION_TYPE = ConnectionType::SQLITE3;
    public const string FILENAME = 'main.sqlite3';
}
