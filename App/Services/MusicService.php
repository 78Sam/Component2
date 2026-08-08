<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Song;
use ComponentPHP\Files\FileSystem;

class MusicService
{
    private FileSystem $fileSystem;

    /** @var array<int, string> */
    private array $config;

    public function __construct() {
        $this->fileSystem = new FileSystem();
        $this->config = require_once FileSystem::PUBLIC_FOLDER . '/Assets/Music/Schema.php';
    }

    /**
     * @return list<Song>
     */
    public function getAllSongs(): array
    {
        $songs = [];
        foreach ($this->fileSystem->iterate('Assets/Music') as $file)
        {
            if ($file->getExtension() !== "mp3")
            {
                continue;
            }

            $path = $file->getRealPath();
            $songs[] = new Song(
                $this->fileSystem->toRelativePath($path),
                $this->filenameNoExtension($file),
                $this->getArtistName($path),
            );
        }

        return $songs;
    }

    /**
     * @return list<string>
     */
    public function getAllArtists(): array
    {
        return array_values($this->config);
    }

    private function getArtistName(string $songPath): string
    {
        $artistKey = array_last(explode('/', dirname($songPath)));
        
        return $this->config[$artistKey] ?? 'Unknown artist';
    }

    private function filenameNoExtension(\SplFileInfo $file): string
    {
        return $file->getBasename(".{$file->getExtension()}");
    }
}
