<?php

declare(strict_types=1);

use ComponentPHP\Debug\DebugMetrics;

function dump(mixed ...$values): void
{
    $debugFrame = DebugMetrics::getBacktrace();

    echo "<pre>{$debugFrame}\n\n";
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
        |> (fn(string $val) => str_replace(DIRECTORY_SEPARATOR, '/', $val))
        |> (fn(string $val) => substr($val, 0, -4))
        |> (fn(string $val) => str_replace(CPHP_ROOT_DIR . '/', '', $val))
        |> (fn(string $val) => str_replace('/', '\\', $val));

    foreach (CPHP_NAMESPACE_ALIASES as $folder => $namespace) {
        if (str_starts_with($class, $folder)) {
            return $namespace . substr($class, strlen($folder));
        }
    }

    return $class;
}

function nanoToSeconds(int $nanoSeconds, int $rounding = 5): float
{
    return round($nanoSeconds / (10 ** 9), $rounding);
}
