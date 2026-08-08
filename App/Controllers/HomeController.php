<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Views\Music;
use ComponentPHP\Routing\Attributes\Route;
use ComponentPHP\Routing\Controllers\AbstractController;
use ComponentPHP\Routing\Models\Response;

final class HomeController extends AbstractController
{
    #[Route(route: '/', name: 'app_home', HTTPVerbs: ['GET'])]
    public function view(): Response
    {
        return new Response(new Music()->main());
    }

    #[Route(route: '/hx/songs', name: 'hx_songs', HTTPVerbs: ['GET'])]
    public function hxSongs(): Response
    {
        return new Response(new Music()->songs());
    }

    #[Route(route: '/hx/artists', name: 'hx_artists', HTTPVerbs: ['GET'])]
    public function hxArtists(): Response
    {
        return new Response(new Music()->artists());
    }
}
