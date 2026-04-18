<?php

declare(strict_types=1);

namespace App\Controllers\Afdbm;

use ComponentPHP\Routing\Attributes\Route;
use ComponentPHP\Routing\Controllers\AbstractController;

final class SamController extends AbstractController
{
    #[Route('/sam', 'app_sam')]
    public function test()
    {
        dump('Hello');
    }
}
