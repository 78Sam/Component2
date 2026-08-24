<?php

declare(strict_types=1);

namespace Core\Debug\Models;

use Core\Debug\DebugMetrics;

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
    public function since(PerformanceSlice $performanceSlice, int $secondsPrecision = 5): array
    {
        $nanosecondDifference = $this->sliceTaken - $performanceSlice->sliceTaken;

        return [
            'nanoseconds' => $nanosecondDifference,
            'seconds' => DebugMetrics::nanoToSeconds($nanosecondDifference, $secondsPrecision),
        ];
    }
}
