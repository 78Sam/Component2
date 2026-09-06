<?php

declare(strict_types=1);

namespace Core\Utility\Resolvers;

use Core\Utility\Resolvers\Exceptions\RequiredKeyMissingException;
use Core\Utility\Resolvers\Exceptions\UndefinedResolverException;

abstract class AbstractResolver
{
    /** @var list<\Exception> */
    private array $errors = [];

    public function __construct(
        public readonly bool $removeUndefined = true,
        public readonly bool $throwErrors = true,
    ) {}

    /**
     * @return array<string, callable(mixed $value): mixed>
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

        if ($this->removeUndefined) {
            $providedValues = array_filter(
                $providedValues,
                fn(string|int $key): bool => array_key_exists($key, $expectedValues),
                2,
            );
        }

        foreach ($expectedValues as $expectedKey => $expectedType) {
            $resolver = ltrim($expectedType, '?');
            $isRequired = $resolver === $expectedType;

            $isPresent = array_key_exists($expectedKey, $providedValues);

            if ($isRequired && !$isPresent) {
                $this->error(new RequiredKeyMissingException("{$expectedKey}"));

                continue;
            }

            if (!$isRequired && !$isPresent) {
                $providedValues[$expectedKey] = null;

                continue;
            }

            $value = $providedValues[$expectedKey];
            if ($value === null && !$isRequired) {
                continue;
            }

            $providedValues[$expectedKey] = $this->getResolver($resolver)($value);
        }

        return $providedValues;
    }

    /**
     * @return list<\Exception>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    protected function error(\Exception $exception): void
    {
        if ($this->throwErrors) {
            throw $exception;
        }

        $this->errors[] = $exception;
    }

    /**
     * @return callable(mixed $value): mixed
     */
    private function getResolver(string $type): callable
    {
        $resolver = $this->getResolvers()[$type] ?? null;
        if ($resolver === null) {
            throw new UndefinedResolverException($type);
        }

        return $resolver;
    }
}
