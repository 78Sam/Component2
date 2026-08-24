<?php

declare(strict_types=1);

if (!function_exists('frankenphp_log'))
{
    define('FRANKENPHP_LOG_LEVEL_DEBUG', -4);
    define('FRANKENPHP_LOG_LEVEL_INFO', 0);
    define('FRANKENPHP_LOG_LEVEL_WARN', 4);
    define('FRANKENPHP_LOG_LEVEL_ERROR', 8);

    function frankenphp_log(string $message, int $level = FRANKENPHP_LOG_LEVEL_INFO, array $context = []): void {}
}

if (!function_exists('frankenphp_handle_request'))
{
    function frankenphp_handle_request(callable $requestHandler): bool
    {
        return true;
    }
}
