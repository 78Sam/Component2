<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Controllers;

use ComponentPHP\Routing\Models\Request;
use ComponentPHP\Routing\Router;

abstract class AbstractController
{
    final public function __construct(
        protected readonly Router $router,
        protected readonly Request $request,
    ) {}
}
