<?php

declare(strict_types=1);

namespace ComponentPHP\Debug;

use ComponentPHP\Debug\Models\Backtrace;
use ComponentPHP\Debug\Models\PerformanceSlice;

class DebugMetrics
{
    public static function getBacktrace(int $steps = 2): Backtrace
    {
        $backtrace = debug_backtrace(limit: $steps);

        /** @var ?string $file */
        $file = $backtrace[$steps - 1]['file'] ?? null;

        /** @var ?int $line */
        $line = $backtrace[$steps - 1]['line'] ?? null;

        return new Backtrace(file: $file, line: $line);
    }

    public static function getPerformanceSlice(string $description = 'Arbitrary performance slice'): PerformanceSlice
    {
        return new PerformanceSlice(
            sliceTaken: (int) hrtime(true),
            memoryUsage: memory_get_usage(true),
            peakMemoryUsage: memory_get_peak_usage(true),
            description: $description,
        );
    }

    public static function nanoToSeconds(int $nanoseconds, int $precision = 5): float
    {
        return round($nanoseconds / (10 ** 9), $precision);
    }
}
