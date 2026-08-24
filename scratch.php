<?php

declare(strict_types=1);

class TestException extends \Exception
{
    public function __construct(
        string $message,
        mixed $value,
        int $code = 0,
        \Throwable|null $previous = null,
    ) {
        return parent::__construct($message, $code, $previous);
    }

    #[Override]
    public function __toString(): string
    {
        return "lol";
    }
}

throw new \TestException('hi');
