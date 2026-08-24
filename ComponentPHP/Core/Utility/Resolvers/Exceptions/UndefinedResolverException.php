<?php

declare(strict_types=1);

namespace ComponentPHP\Utility\Resolvers\Exceptions;

class UndefinedResolverException extends \Exception
{
    public function __construct(
        public readonly string $type,
        int $code = 0,
        \Throwable|null $previous = null,
    ) {
        parent::__construct(
            "The resolver for type '{$this->type}' is not defined",
            $code,
            $previous
        );
    }
}
