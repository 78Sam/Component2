<?php

declare(strict_types=1);

class ValidationException extends \Exception
{}

class MissingKeyException extends \Exception
{}

/**
 * @template V
 */
abstract class Requirement
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

/**
 * @extends Requirement<string|ValidationException>
 */
class StringRequirement extends Requirement
{
    #[\Override]
    public function validate(mixed $value): void
    {
        if (!is_string($value))
        {
            $this->value = new ValidationException('Not a string lol');

            return;
        }

        $this->value = $value;
    }
}

/**
 * @extends Requirement<int|ValidationException>
 */
class IntOrStringIntRequirement extends Requirement
{
    #[\Override]
    public function validate(mixed $value): void
    {
        if (is_string($value))
        {
            $isNegative = false;
            if (substr($value, 0, 1) === '-')
            {
                $isNegative = true;
                $value = substr($value, 1);
            }

            if (ctype_digit($value))
            {
                $value = (int) $value;
                if ($isNegative)
                {
                    $value = -$value;
                }
            }
        }

        if (!is_int($value))
        {
            $this->value = new ValidationException('bad');

            return;
        }

        $this->value = $value;
    }
}

function tester(array $requirements, array $provided): void
{
    foreach ($requirements as $requirement)
    {
        $key = $requirement->key;
        if (!array_key_exists($key, $provided))
        {
            $requirement->markMissing();

            continue;
        }

        $requirement->validate($provided[$key]);
    }
}

$requirements = [
    'SERVER_NAME' => new StringRequirement('SERVER_NAME'),
    'REQUEST_SCHEME' => new StringRequirement('REQUEST_SCHEME'),
    'HTTPS' => new StringRequirement('HTTPS'),
    'REQUEST_URI' => new StringRequirement('REQUEST_URI'),
    'SERVER_PORT' => new IntOrStringIntRequirement('SERVER_PORT'),
    'QUERY_STRING' => new IntOrStringIntRequirement('QUERY_STRING'),
    'REQUEST_METHOD' => new IntOrStringIntRequirement('REQUEST_METHOD'),
    'REQUEST_TIME' => new IntOrStringIntRequirement('REQUEST_TIME'),
];

tester(
    $requirements,
    $_SERVER,
);

$x = $requirements['SERVER_NAME']->getValue();
