<?php

declare(strict_types=1);

namespace ComponentPHP\Logging;

use ComponentPHP\Debug\DebugMetrics;
use ComponentPHP\Logging\Models\LoggingChannels;
use ComponentPHP\Logging\Models\LoggingLevel;

class Logger
{
    private const string COMBINED_LOG_DIRECTORY = CPHP_LOG_DIR . '/Combined';

    public function __construct(
        public LoggingChannels $channel = LoggingChannels::Core,
    ) {}

    public function log(string $message, LoggingLevel $level = LoggingLevel::Info): self
    {
        self::singleLog($message, level: $level, channel: $this->channel);

        return $this;
    }

    public static function singleLog(
        string $message,
        LoggingLevel $level = LoggingLevel::Info,
        LoggingChannels $channel = LoggingChannels::Core,
    ): void {
        $directory = self::setupDirectories($channel);

        $datetime = new \DateTime(timezone: new \DateTimeZone(CPHP_TIMEZONE))->format('d-m-Y H:i:s');
        $debugFrame = DebugMetrics::getBacktrace(steps: 3);

        // TODO: Is this essentially performing I/O for each log, could be costly
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
        if (CPHP_IS_DEV) {
            error_log("{$log} ({$channel->value})");
        }
    }

    private static function setupDirectories(LoggingChannels $channel): string
    {
        $directory = CPHP_LOG_DIR . "/{$channel->value}";
        if (!is_dir($directory)) {
            mkdir($directory, recursive: true);
        }

        if (!is_dir(self::COMBINED_LOG_DIRECTORY)) {
            mkdir(self::COMBINED_LOG_DIRECTORY, recursive: true);
        }

        return $directory;
    }
}
