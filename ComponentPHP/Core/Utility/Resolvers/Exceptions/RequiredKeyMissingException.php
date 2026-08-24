<?php

declare(strict_types=1);

namespace ComponentPHP\Utility\Resolvers\Exceptions;

class RequiredKeyMissingException extends \Exception
{
    public function __construct(
        public readonly string $key,
        int $code = 0,
        \Throwable|null $previous = null,
    ) {
        parent::__construct(
            "The required key '{$this->key}' was not provided",
            $code,
            $previous
        );
    }
}
