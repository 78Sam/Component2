<?php

declare(strict_types=1);

namespace ComponentPHP\Utility\Resolvers;

use ComponentPHP\Utility\Resolvers\Exceptions\RequiredKeyMissingException;
use ComponentPHP\Utility\Resolvers\Exceptions\UndefinedResolverException;

abstract class AbstractResolver
{
    /** @var list<\Exception> */
    public array $errors = [];

    public function __construct(
        public readonly bool $removeUndefined = true,
        public readonly bool $throwErrors = true,
    ) {
    }

    /**
     * @return array<string, callable(mixed $value, string $type): mixed>
     */
    abstract protected function getResolvers(): array;

    /**
     * @template T of array-key
     * @template V of mixed
     * 
     * @param array<array-key, string> $expectedValues
     * @param array<T, V> $providedValues
     * 
     * @throws \Exception if $this->throwErrors is true
     * @return array<T, V>
     */
    public function resolve(array $expectedValues, array $providedValues): array
    {
        $this->errors = [];

        if ($this->removeUndefined)
        {
            $providedValues = array_filter($providedValues, fn(string|int $key): bool => array_key_exists($key, $expectedValues), 2);
        }

        foreach ($expectedValues as $expectedKey => $expectedType)
        {
            $resolver = ltrim($expectedType, '?');
            $isRequired = $resolver === $expectedType;

            $isPresent = array_key_exists($expectedKey, $providedValues);
            if ($isPresent)
            {
                $value = $providedValues[$expectedKey];
                if ($value === null && !$isRequired)
                {
                    continue;
                }
                // TODO: Is it really worth sending the type? (Update DocBlocks if not)
                $providedValues[$expectedKey] = $this->getResolver($resolver)($value, gettype($value));

                continue;
            }

            if ($isRequired)
            {
                $this->error(new RequiredKeyMissingException($expectedKey));

                continue;
            }

            $providedValues[$expectedKey] = null;
        }

        return $providedValues;
    }

    protected function error(\Exception $exception): void
    {
        if ($this->throwErrors)
        {
            throw $exception;
        }

        $this->errors[] = $exception;
    }

    /**
     * @return callable(mixed $value, string $type): mixed
     */
    private function getResolver(string $type): callable
    {
        $resolver = $this->getResolvers()[$type] ?? null;
        if ($resolver === null)
        {
            throw new UndefinedResolverException($type);
        }

        return $resolver;
    }
}
