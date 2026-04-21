<?php

declare(strict_types=1);

namespace ComponentPHP\Logging;

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
        $debugFrame = get_debug_backtrace(step: 3);
        $file = $debugFrame['file'];
        $line = $debugFrame['line'];

        $datetime = new \DateTime(timezone: new \DateTimeZone(CPHP_TIMEZONE));
        $datetimeFormatted = $datetime->format('d-m-Y H:i:s');

        $dir = CPHP_LOG_DIR . "/{$channel->value}";
        if (!is_dir($dir)) {
            mkdir($dir, recursive: true);
        }

        if (!is_dir(self::COMBINED_LOG_DIRECTORY)) {
            mkdir(self::COMBINED_LOG_DIRECTORY, recursive: true);
        }

        // TODO: Is this essentially performing I/O for each log, could be costly
        $log = "{$datetimeFormatted} {$file}::{$line} | [{$level->value}] {$message}";
        error_log("{$log}\n", message_type: 3, destination: "{$dir}/log.log");
        error_log(
            "{$log} ({$channel->value})\n",
            message_type: 3,
            destination: self::COMBINED_LOG_DIRECTORY . '/log.log',
        );
        if (CPHP_IS_DEV) {
            error_log("{$log} ({$channel->value})");
        }
    }
}
