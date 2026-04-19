<?php

declare(strict_types=1);

/**
 * @return array{file: string, line: int}
 */
function get_debug_backtrace(int $step = 2): array
{
    /** @var array[] $backtrace */
    $backtrace = debug_backtrace(limit: $step);
    if (count($backtrace) < $step) {
        return [
            'file' => 'unknown_file',
            'line' => -1,
        ];
    }

    return [
        'file' => $backtrace[$step - 1]['file'] ?? 'unknown_file',
        'line' => $backtrace[$step - 1]['line'] ?? -1,
    ];
}

function dump(mixed ...$values): void
{
    $debugFrame = get_debug_backtrace();
    $file = $debugFrame['file'];
    $line = $debugFrame['line'];

    echo '<pre>';
    echo "{$file}::{$line}\n\n";
    foreach ($values as $value) {
        $data = print_r($value, return: true);
        echo htmlspecialchars($data);
        echo '<br>';
    }
    echo "\n\n</pre>";
}

function path_to_class(string $path): string
{
    $class = $path
        |> (fn($val) => str_replace(DIRECTORY_SEPARATOR, '/', $val))
        |> (fn($val) => substr($val, 0, -4))
        |> (fn($val) => str_replace(CPHP_ROOT_DIR . '/', '', $val))
        |> (fn($val) => str_replace('/', '\\', $val));

    foreach (CPHP_NAMESPACE_ALIASES as $folder => $namespace) {
        if (str_starts_with($class, $folder)) {
            return $namespace . substr($class, strlen($folder));
        }
    }

    return $class;
}
