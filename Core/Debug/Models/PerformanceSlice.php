<?php

declare(strict_types=1);

namespace ComponentPHP\Debug\Models;

use ComponentPHP\Debug\DebugMetrics;

readonly class PerformanceSlice
{
    public function __construct(
        public int $sliceTaken,
        public int $memoryUsage,
        public int $peakMemoryUsage,
        public string $description,
    ) {}

    /**
     * @return array{'nanoseconds': int, 'seconds': float}
     */
    public function since(PerformanceSlice $performanceSlice): array
    {
        $nanosecondDifference = $this->sliceTaken - $performanceSlice->sliceTaken;

        return [
            'nanoseconds' => $nanosecondDifference,
            'seconds' => DebugMetrics::nanoToSeconds($nanosecondDifference),
        ];
    }
}
