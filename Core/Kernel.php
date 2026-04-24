<?php

declare(strict_types=1);

namespace ComponentPHP;

use ComponentPHP\Logging\Logger;
use ComponentPHP\Logging\Models\LoggingChannels;
use ComponentPHP\Logging\Models\LoggingLevel;
use ComponentPHP\Routing\Router;

class Kernel
{
    private Logger $logger;
    private Router $router;

    public function __construct()
    {
        $mode = CPHP_IS_DEV ? 'DEV' : 'PROD';

        $this->logger = new Logger(LoggingChannels::Core);
        $this->logger->log("Starting Kernel in {$mode} mode");

		$debugMetrics = [
			'startTime' => hrtime(true),
			'startMemory' => memory_get_usage(true),
		];

        try {
            $this->router = new Router();
            $this->router->init();

            $response = $this->router->handleRequest();
            $this->router->handleResponse($response, $debugMetrics);

            $timeTaken = nanoToSeconds(hrtime(true) - $debugMetrics['startTime']);
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
