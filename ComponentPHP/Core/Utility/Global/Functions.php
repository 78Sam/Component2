<?php

declare(strict_types=1);

use Core\Debug\DebugMetrics;

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
