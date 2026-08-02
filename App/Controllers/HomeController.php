<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Views\Home;
use ComponentPHP\Routing\Attributes\Route;
use ComponentPHP\Routing\Controllers\AbstractController;
use ComponentPHP\Routing\Models\Response;

final class HomeController extends AbstractController
{
    #[Route(route: '/', name: 'app_home', HTTPVerbs: ['GET'])]
    public function view(): Response
    {
        return new Response(new Home()->home());
    }
}
