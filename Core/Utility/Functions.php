<?php

declare(strict_types=1);

/**
 * @return array{file: string, line: int}
 */
function getDebugBacktrace(): array
{
    /** @var array[] $backtrace */
	$backtrace = debug_backtrace(limit: 2);
	if (count($backtrace) < 2)
	{
		return [
            'file' => 'unknown_file',
            'line' => -1,
        ];
	}

    return [
        'file' => $backtrace[1]['file'] ?? 'unknown_file',
        'line' => $backtrace[1]['line'] ?? -1,
    ];
}


function dump(mixed ...$values): void
{
    $debugFrame = getDebugBacktrace();
    $file = $debugFrame['file'];
    $line = $debugFrame['line'];

	echo '<pre>';
	echo "{$file}::{$line}\n\n";
	foreach ($values as $value)
	{
		$data = print_r($value, return: true);
        echo htmlspecialchars($data);
		echo '<br>';
	}
	echo "\n\n</pre>";
}

// TODO: Change channel used by logging function to expect enum not string
enum LoggingChannels: string
{
	case Core = 'Core';
	case Templating = 'Templating';
	case Router = 'Router';
}


function cphpLog(string $message, string $level = 'info', LoggingChannels $channel = LoggingChannels::Core): void
{
    $debugFrame = getDebugBacktrace();
    $file = $debugFrame['file'];
    $line = $debugFrame['line'];

    $datetime = new \DateTime(timezone: new \DateTimeZone(CPHP_TIMEZONE));
    $datetimeFormatted = $datetime->format('d-m-Y H:i:s');

    $dir = CPHP_LOG_DIR . "/{$channel->value}";
    if (!file_exists($dir))
    {
        mkdir($dir, recursive: true);
    }

    $log = "{$datetimeFormatted} {$file}::{$line} | [{$level}] {$message}";

    // TODO: Is this essentially performing I/O for each log, could be costly
    error_log("{$log}\n", message_type: 3, destination: "{$dir}/log.log");
}


function pathToClass(string $path)
{
	$class = $path
		|> (fn($val) => str_replace(DIRECTORY_SEPARATOR, '/', $val))
		|> (fn($val) => substr($val, 0, -4))
		|> (fn($val) => str_replace(CPHP_ROOT_DIR . '/', '', $val))
		|> (fn($val) => str_replace('/', '\\', $val))
	;

	foreach (CPHP_NAMESPACE_ALIASES as $folder => $namespace)
	{
		if (str_starts_with($class, $folder))
		{
			cphpLog("replace: {$class}", channel: LoggingChannels::Router);
			$class = $namespace . substr($class, strlen($folder));
			cphpLog("After: {$class}", channel: LoggingChannels::Router);
		}
	}

	return $class;
}
