<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Views\Home;
use ComponentPHP\Routing\Attributes\Route;
use ComponentPHP\Routing\Controllers\AbstractController;
use ComponentPHP\Routing\Models\Response;

class HomeController extends AbstractController
{
    #[Route(route: '/', name: 'app_home')]
    public function index(): Response
    {
        $view = new Home();
        $view->run();

        return new Response('');
    }

    #[Route(route: '/random-number', name: 'app_randomNumber')]
    public function randomNumber(): Response
    {
        return new Response((string) rand(0, 500));
    }

    #[Route(route: '/validate', name: 'app_validate')]
    public function validate(): Response
    {
        $username = $_POST['username'];
        $response = "Your username '{$username}' ";
        $response .= \strlen($username) > 5 ? 'is long enough :)' : 'is NOT long enough :(';

        return new Response($response);
    }

    #[Route(route: '/uma', name: 'app_uma')]
    public function uma(): Response
    {
        return new Response('I love uma <3', responseCode: 200);
    }
}
