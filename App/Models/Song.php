<?php

declare(strict_types=1);

namespace App\Models;

readonly class Song
{
    public function __construct(
        public string $path,
        public string $title,
        public string $artist,
    ) {
    }
}
