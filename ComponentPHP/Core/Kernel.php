<?php

declare(strict_types=1);

namespace Core;

use Core\Debug\DebugMetrics;
use Core\Routing\Models\Request;
use Core\Routing\Router;
use Core\Utility\Validators\Services\ValidatorService;
use Core\Utility\Validators\Types\IntOrStringIntValidator;
use Core\Utility\Validators\Types\StringValidator;

class Kernel
{
    public readonly Router $router;

    public function __construct(
        public readonly string $workerId,
    ) {
        $this->router = new Router();
    }

    public function boot(): void
    {
        frankenphp_log('Booting kernel', context: ['workerId' => $this->workerId]);
    }

    public function handleRequest(
        array $server,
        array $get,
        array $post,
        array $files,
        array $cookies,
    ): void
    {
        $startOfHandleRequestPerformanceSlice = DebugMetrics::getPerformanceSlice('Start of handleRequest()');

        frankenphp_log('Handling request', context: ['workerId' => $this->workerId]);

        $request = $this->router->buildRequest($server, $get, $post, $files, $cookies);
        dump($request);

        $endOfHandleRequestPerformanceSlice = DebugMetrics::getPerformanceSlice('End of handleRequest()');
        dump("Request took:", $endOfHandleRequestPerformanceSlice->since($startOfHandleRequestPerformanceSlice, 9));

        echo 'Yay';
    }

    public function shutdown(): void
    {
        frankenphp_log('Shutting down', context: ['workerId' => $this->workerId]);
    }
}
