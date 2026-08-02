<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Song;
use ComponentPHP\Files\FileSystem;

class MusicService
{
    private FileSystem $fileSystem;

    public function __construct() {
        $this->fileSystem = new FileSystem();
    }

    /**
     * @return list<Song>
     */
    public function getAllMusic(): array
    {
        $songs = [];
        foreach ($this->fileSystem->iterate('Assets/Music') as $file)
        {
            if ($file->getExtension() !== "mp3")
            {
                continue;
            }

            $songs[] = new Song($this->fileSystem->toRelativePath($file->getRealPath()), $file->getFilename(), "sam");
        }

        return $songs;
    }
}
