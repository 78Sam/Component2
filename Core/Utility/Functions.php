<?php

declare(strict_types=1);

function dump(mixed ...$values): void
{
	/** @var list<array> $backtrace */
	$backtrace = debug_backtrace(limit: 1);
	if (count($backtrace) === 0)
	{
		return;
	}

	/** @var array<string, string> $frame */
	$frame = $backtrace[0];
	$file = $frame['file'] ?? 'unknown_file';
	$line = $frame['line'] ?? 'unknown_line';

	echo '<pre>';
	echo "{$file}::{$line}\n\n";
	foreach ($values as $value)
	{
		print_r($value);
		echo '<br>';
	}
	echo "\n\n</pre>";
}


function pathToClass(string $path)
{
	$class = $path
		|> (fn($val) => str_replace(DIRECTORY_SEPARATOR, '/', $val))
		|> (fn($val) => substr($val, 0, -4))
		|> (fn($val) => str_replace(COMPONENT_ROOT_DIR . '/', '', $val))
		|> (fn($val) => str_replace('/', '\\', $val))
	;

	foreach (NAMESPACE_ALIASES as $folder => $namespace)
	{
		if (str_starts_with($class, $folder))
		{
			error_log("replace: {$class}");
			$class = $namespace . substr($class, strlen($folder));
			error_log("After: {$class}");
		}
	}

	return $class;
}
