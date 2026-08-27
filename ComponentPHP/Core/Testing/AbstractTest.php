<?php

declare(strict_types=1);

namespace Core\Testing;

abstract class AbstractTest
{
    public function assertEquals(mixed $value1, mixed $value2, string $message): void
    {
        if ($value1 !== $value2)
        {
            throw new \AssertionError($message);
        }
    }
}
