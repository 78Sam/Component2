<?php

declare(strict_types=1);

namespace Core;

use Core\Debug\DebugMetrics;
use Core\Routing\Models\Request;
use Core\Routing\RouterInterface;
use Core\Utility\Requirements\Services\RequirementService;
use Core\Utility\Requirements\Specifics\IntOrStringIntRequirement;
use Core\Utility\Requirements\Specifics\StringRequirement;

class Kernel
{
    public readonly RouterInterface $router;

    public function __construct(
        public readonly string $workerId,
    ) {
        // $this->router = 
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

        $requirements = [
            'SERVER_NAME' => new StringRequirement('SERVER_NAME'),
            'REQUEST_SCHEME' => new StringRequirement('REQUEST_SCHEME'),
            'HTTPS' => new StringRequirement('HTTPS'),
            'REQUEST_URI' => new StringRequirement('REQUEST_URI'),
            'SERVER_PORT' => new IntOrStringIntRequirement('SERVER_PORT'),
            'QUERY_STRING' => new StringRequirement('QUERY_STRING'),
            'REQUEST_METHOD' => new StringRequirement('REQUEST_METHOD'),
            'REQUEST_TIME' => new IntOrStringIntRequirement('REQUEST_TIME'),
        ];

        RequirementService::validate($requirements, $server);

        dump($requirements);

        $httpsScheme = in_array($requirements['HTTPS']->getValueWithDefault(''), ['', 'off'], true) ? 'HTTP' : 'HTTPS';
        $request = new Request(
            host: $requirements['SERVER_NAME']->getValue(),
            scheme: strtoupper($requirements['REQUEST_SCHEME']->getValueWithDefault($httpsScheme)),
            path: parse_url($requirements['REQUEST_URI']->getValue(), PHP_URL_PATH),
            port: $requirements['SERVER_PORT']->getValue(),
            queryString: $requirements['QUERY_STRING']->getValue(),
            method: $requirements['REQUEST_METHOD']->getValue(),
            requestTime: $requirements['REQUEST_TIME']->getValue(),
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
