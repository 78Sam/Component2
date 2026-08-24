<?php

declare(strict_types=1);

namespace ComponentPHP\Logging;

use ComponentPHP\Debug\DebugMetrics;
use ComponentPHP\Logging\Models\LoggingChannel;
use ComponentPHP\Logging\Models\LoggingLevel;
use ComponentPHP\Utility\Config;

class Logger
{
    private const string COMBINED_LOG_DIRECTORY = Config::LOG_DIR . '/Combined';

    public function __construct(
        public LoggingChannel $channel = LoggingChannel::Core,
    ) {}

    public function log(string $message, LoggingLevel $level = LoggingLevel::Info): self
    {
        self::singleLog($message, level: $level, channel: $this->channel);

        return $this;
    }

    public static function singleLog(
        string $message,
        LoggingLevel $level = LoggingLevel::Info,
        LoggingChannel $channel = LoggingChannel::Core,
    ): void {
        $directory = self::setupDirectories($channel);

        $datetime = new \DateTime(timezone: new \DateTimeZone(Config::TIMEZONE))->format('d-m-Y H:i:s');
        $debugFrame = DebugMetrics::getBacktrace(steps: 3);

        // TODO(Sam): Is this essentially performing I/O for each log, could be costly
        $log = "{$datetime} {$debugFrame} | [{$level->value}] {$message}";

        // Channel log file
        error_log("{$log}\n", message_type: 3, destination: "{$directory}/log.log");

        // Combined log file
        error_log(
            "{$log} ({$channel->value})\n",
            message_type: 3,
            destination: self::COMBINED_LOG_DIRECTORY . '/log.log',
        );

        // Terminal Log
        if (Config::IS_DEV) {
            error_log("{$log} ({$channel->value})");
        }
    }

    private static function setupDirectories(LoggingChannel $channel): string
    {
        $directory = Config::LOG_DIR . "/{$channel->value}";
        if (!is_dir($directory)) {
            mkdir($directory, recursive: true);
        }

        if (!is_dir(self::COMBINED_LOG_DIRECTORY)) {
            mkdir(self::COMBINED_LOG_DIRECTORY, recursive: true);
        }

        return $directory;
    }
}
