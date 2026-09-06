<?php

declare(strict_types=1);

namespace Core\Utility\Validators\Types;

use Core\Utility\Validators\Exceptions\MissingKeyException;

/**
 * @template V
 */
abstract class AbstractValidator
{
    /** @var V|MissingKeyException */
    protected mixed $value;

    public function __construct(
        public readonly string|int $key,
    ) {
    }

    public function isMissing(): bool
    {
        return $this->value instanceof MissingKeyException;
    }

    public function markMissing(): self
    {
        $this->value = new MissingKeyException("Key '{$this->key}' is required but has not been provided");

        return $this;
    }

    /**
     * @throws MissingKeyException
     *
     * @return V
     */
    public function getValue(): mixed
    {
        if ($this->value instanceof MissingKeyException)
        {
            throw $this->value;
        }

        return $this->value;
    }

    /**
     * @template T
     *
     * @param T $default
     *
     * @return V|T
     */
    public function getValueWithDefault(mixed $default = null): mixed
    {
        return $this->value instanceof MissingKeyException ? $default : $this->value;
    }

    abstract public function validate(mixed $value): void;
}
