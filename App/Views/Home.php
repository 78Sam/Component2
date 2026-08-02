<?php

declare(strict_types=1);

namespace App\Views;

use App\Models\Song;
use App\Services\MusicService;
use ComponentPHP\Components\AbstractTemplate;
use ComponentPHP\Components\Models\Component;

class Home extends AbstractTemplate
{
    private MusicService $musicService;

    public function __construct()
    {
        parent::__construct();

        $this->musicService = new MusicService();
    }

    public function home(): Component
    {
        $songs = array_map(fn(Song $song) => (array) $song, $this->musicService->getAllMusic());
        $songComponents = $this->stack('song', $songs);

        return $this
			->get('app')
            ->fill('body', $this->get('songs')->fill('songs', $this->collect($songComponents)))
		;
    }

    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile('base.html');
        $this->loadFile('Home/view.html');
    }
}
