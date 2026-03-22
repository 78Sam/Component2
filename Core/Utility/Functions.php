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
