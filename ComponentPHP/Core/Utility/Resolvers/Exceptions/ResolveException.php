<?php

declare(strict_types=1);

namespace Core\Utility\Resolvers\Exceptions;

class ResolveException extends \Exception
{
    public function __construct(
        public readonly mixed $value,
        public readonly string $providedType,
        public readonly string $expectedType,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            "Value was expected to be of type '{$this->expectedType}' but received '{$this->providedType}'",
            $code,
            $previous,
        );
    }
}
