<?php

declare(strict_types=1);

namespace ComponentPHP;

use ComponentPHP\Debug\DebugMetrics;
use ComponentPHP\Debug\Models\PerformanceSlice;
use ComponentPHP\Logging\Logger;
use ComponentPHP\Logging\Models\LoggingChannels;
use ComponentPHP\Logging\Models\LoggingLevel;
use ComponentPHP\Routing\Router;
use ComponentPHP\Utility\Config;

class Kernel
{
    /** @var PerformanceSlice[] $performanceSlices */
    private array $performanceSlices;
    private Logger $logger;
    private Router $router;

    public function __construct()
    {
        $this->performanceSlices[] = DebugMetrics::getPerformanceSlice('Start of the kernel');

        $mode = Config::IS_DEV ? 'DEV' : 'PROD';

        $this->logger = new Logger(LoggingChannels::Core);
        $this->logger->log("Starting Kernel in {$mode} mode");

        try {
            $this->router = new Router();
            $this->performanceSlices[] = DebugMetrics::getPerformanceSlice('Initialized router');

            $response = $this->router->handleRequest();
            $this->performanceSlices[] = DebugMetrics::getPerformanceSlice('Handled request');

            $this->router->handleResponse($response);
            $this->performanceSlices[] = DebugMetrics::getPerformanceSlice('Handled response');

            $timeTaken = array_last($this->performanceSlices)->since($this->performanceSlices[0])['seconds'];
            $this->logger->log("Request and response complete in {$timeTaken}s");
        } catch (\Throwable $exception) {
            $this->except($exception);
        }
    }

    private function except(\Throwable $e): never
    {
        // TODO: We could turn this into a response and do $this->router->handleResponse(...);
        $this->logger->log($e->getMessage(), level: LoggingLevel::Error);
        http_response_code($e->getCode());
        dump($e);

        exit();
    }
}
