<?php

declare(strict_types=1);

namespace ComponentPHP\Routing\Exceptions;

class PageNotFoundException extends RoutingException
{
    public function __construct(
        public string $route,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        return parent::__construct($message, $code, $previous);
    }
}
