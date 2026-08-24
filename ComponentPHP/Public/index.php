<?php

declare(strict_types=1);

use Core\Kernel;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$kernel = new Kernel(bin2hex(random_bytes(4)));
$kernel->boot();

$handler = static function() use ($kernel)
{
    try
    {
        $kernel->handleRequest($_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);
    }
    catch (\Throwable $exception)
    {
        dump($exception);
    }
};

$totalRequests = 0;
while (true)
{
    $keepRunning = frankenphp_handle_request($handler);

    gc_collect_cycles();

    if (!$keepRunning)
    {
        break;
    }
}

$kernel->shutdown();
