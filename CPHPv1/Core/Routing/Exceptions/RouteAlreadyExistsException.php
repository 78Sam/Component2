<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Exceptions;

use Throwable;

class RouteAlreadyExistsException extends RoutingException
{
    public readonly string $route;
    public readonly string $name;

    public function __construct(
        string $route,
        string $name,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        $this->route = $route;
        $this->route = $name;

        return parent::__construct(message: $message, code: $code, previous: $previous);
    }
}
