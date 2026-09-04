<?php

declare(strict_types=1);

use Core\Debug\DebugMetrics;
use Core\Utility\Config;

if (!function_exists('dump'))
{
    function dump(mixed ...$values): void
    {
        $debugFrame = DebugMetrics::getBacktrace();

        echo "<pre>{$debugFrame}\n\n";
        foreach ($values as $value) {
            $data = var_export($value, return: true);
            echo htmlspecialchars($data);
            echo '<br>';
        }
        echo "\n\n</pre>";
    }
}

function normalisePath(string $path): string
{
    return str_replace('\\', '/', $path);
}

function relativeToAbsolutePath(string $relativePath): string
{
    return Config::ROOT_DIR . '/' . trim(normalisePath($relativePath), '/');
}

/**
 * @return class-string
 */
function fileToClassString(\SplFileInfo $file): ?string
{
    if ($file->getExtension() !== 'php')
    {
        throw new \Exception("Can only convert php files to class-strings, not '{$file->getRealPath()}'");
    }

    $filePath = substr(normalisePath($file->getRealPath()), 0, -4);
    foreach (PSR4_NAMESPACES as $path => $namespace)
    {
        if (str_contains($filePath, $path))
        {
            return str_replace('/', '\\', str_replace($path, $namespace, $filePath));
        }
    }

    return null;
}
