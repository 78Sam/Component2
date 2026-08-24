<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Artist;
use App\Models\Song;
use ComponentPHP\Files\FileSystem;

class MusicService
{
    private FileSystem $fileSystem;

    /** @var array<string, Artist> */
    private array $artists = [];

    public function __construct() {
        $this->fileSystem = new FileSystem();
        $artistMappings = require_once FileSystem::PUBLIC_FOLDER . '/Assets/Music/Schema.php';
        foreach ($artistMappings as $artistKey => $artistName)
        {
            $this->artists[$artistKey] = new Artist($artistKey, $artistName);
        }
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
                $this->getArtistByPath($path),
            );
        }

        return $songs;
    }

    /**
     * @return list<Artist>
     */
    public function getAllArtists(): array
    {
        return array_values($this->artists);
    }

    private function getArtistByPath(string $songPath): Artist
    {
        $artistKey = array_last(explode(DIRECTORY_SEPARATOR, dirname($songPath)));
        if (!array_key_exists($artistKey, $this->artists))
        {
            throw new \Exception("Cannot find artist for key '{$artistKey}'");
        }
        
        return $this->artists[$artistKey];
    }

    private function filenameNoExtension(\SplFileInfo $file): string
    {
        return $file->getBasename(".{$file->getExtension()}");
    }
}
