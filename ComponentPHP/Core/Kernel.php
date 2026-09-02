<?php

declare(strict_types=1);

namespace Core;

use Core\Debug\DebugMetrics;
use Core\Routing\Models\Request;
use Core\Routing\RouterInterface;
use Core\Utility\Resolvers\RequestResolver;

class Kernel
{
    public readonly RouterInterface $router;
    public readonly RequestResolver $requestResolver;

    public function __construct(
        public readonly string $workerId,
    ) {
        // $this->router = 
        $this->requestResolver = new RequestResolver();
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

        $result = $this->requestResolver->resolve(
            [
                'SERVER_NAME' => 'string',
                'REQUEST_SCHEME' => '?string',
                'HTTPS' => '?string',
                'REQUEST_URI' => 'string',
                'SERVER_PORT' => 'int',
                'QUERY_STRING' => 'string',
                'REQUEST_METHOD' => 'string',
                'REQUEST_TIME' => 'int',
            ],
            $server,
        );

        dump($result);

        $httpsScheme = in_array($result['HTTPS'] ?? '', ['', 'off'], true) ? 'HTTP' : 'HTTPS';
        $request = new Request(
            host: $result['SERVER_NAME'],
            scheme: strtoupper($result['REQUEST_SCHEME'] ?? $httpsScheme),
            path: parse_url($result['REQUEST_URI'], PHP_URL_PATH),
            port: $result['SERVER_PORT'],
            queryString: $result['QUERY_STRING'],
            method: $result['REQUEST_METHOD'],
            requestTime: $result['REQUEST_TIME'],
            serverTime: time(),
            get: $get,
            post: $post,
            files: $files,
            cookies: $cookies,
        );

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
