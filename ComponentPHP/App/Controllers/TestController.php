<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Response;

class TestController extends AbstractController
{
    #[Route(['/', '/index'], 'app_index')]
    public function index(): Response
    {
        return new Response('Hello :)');
    }
}
