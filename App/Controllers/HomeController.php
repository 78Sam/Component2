<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Views\Home;
use ComponentPHP\Routing\Attributes\Route;
use ComponentPHP\Routing\Controllers\AbstractController;
use ComponentPHP\Routing\Models\Response;
use ComponentPHP\Utility\Config;

class HomeController extends AbstractController
{
    #[Route(route: '/', name: 'app_home')]
    public function index(): Response
    {
        return new Response(new Home()->home());
    }
}
