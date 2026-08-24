<?php

declare(strict_types=1);

namespace Core\Routing;

use Core\Routing\Models\Request;
use Core\Routing\Models\Response;

interface RouterInterface
{
    public function buildRequest(): Request;

    public function routeRequest(Request $request): Response;
}
