<?php

declare(strict_types=1);

namespace App\Views;

use App\Models\Song;
use App\Services\MusicService;
use ComponentPHP\Components\AbstractTemplate;
use ComponentPHP\Components\Models\Component;

class Music extends AbstractTemplate
{
    private MusicService $musicService;
    private Component $player;
    private Component $navBar;

    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile('app.html');
        $this->loadFile('Core/Containers.html');
        $this->loadFile('Core/Navigation.html');
        $this->loadFile('Music/Player.html');
        $this->loadFile('Music/Pages.html');
    }

    public function __construct()
    {
        parent::__construct();

        $this->musicService = new MusicService();
        $this->player = $this->get('player');
        $this->navBar = $this->get('navbar');
    }

    public function songs(): Component
    {
        $songs = [];
        foreach ($this->musicService->getAllSongs() as $song)
        {
            $songs[] = [
                'title' => $song->title,
                'artist' => $song->artist->name,
                'path' => $song->path,
            ];
        }

        $songsComponent = $this
            ->get('songs')
            ->fill('songs', $this->quickCollect('song', $songs))
        ;

        return $songsComponent;
    }

    public function artists(): Component
    {
        $artists = [];
        foreach ($this->musicService->getAllArtists() as $artist)
        {
            $artists[] = ['artist' => $artist->name];
        }

        $artistsComponent = $this
            ->get('artists')
            ->fill('artists', $this->quickCollect('artist', $artists))
        ;

        return $artistsComponent;
    }

    public function main(): Component
    {
        $mainCss = $this->quickCollect('stylesheet', [
            ['filename' => 'CSS/Music/Main.css'],
            ['filename' => 'CSS/Music/Player/Main.css'],
            ['filename' => 'CSS/Core/Navigation.css'],
        ]);

        $body = $this->collect([
            $this->navBar,
            $this->get('container_main')->fill('content', $this->songs()),
            $this->player,
        ]);

        return $this
            ->get('app')
            ->fill('css', $mainCss)
            ->fill('body', $body)
        ;
    }
}
