<?php

declare(strict_types=1);

namespace ComponentPHP\Routing;

use ComponentPHP\Routing\Models\Request;
use ComponentPHP\Routing\Models\Response;

interface RouterInterface
{
    public function buildRequest(): Request;

    public function routeRequest(Request $request): Response;
}
