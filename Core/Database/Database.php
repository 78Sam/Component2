<?php

declare(strict_types=1);

namespace ComponentPHP\Database;

use App\Config\DatabaseConfig;

class Database
{
    public function __construct()
    {}

    public function connect()
    {
        $dsn = DatabaseConfig::CONNECTION_TYPE->value . ':/' . CPHP_ROOT_DIR . '/App/Database/' . DatabaseConfig::FILENAME;
        dump($dsn);
        $conn = new \PDO($dsn);
        foreach ($conn->query('SELECT * FROM Test') as $row)
        {
            dump($row);
        }
    }
}
