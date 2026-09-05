<?php

declare(strict_types=1);

class Requirement
{
    /**
     * @template T
     * 
     * @var T $value
     */
    public function __construct(
        public readonly string|int $key,
        public readonly string $type,
        public readonly bool $nullable = false,
    ) {
    }
}

class Satisfaction implements ArrayAccess
{
    public array $container = [];

    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        throw new \Exception('Not implemented');
    }

    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        throw new \Exception('Not implemented');
    }

    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \Exception('Not implemented');
    }

    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        throw new \Exception('Not implemented');
    }
}

/**
 * @param list<Requirement> $expected
 */
function testme(array $expected, array $provided): WeakMap
{
    $satisfaction = new WeakMap();
    foreach ($expected as $requirement)
    {
        if (!array_key_exists($requirement->key, $provided))
        {
            throw new \Exception('Required key missing');
        }

        $value = $provided[$requirement->key];
        if (gettype($value) !== $requirement->type)
        {
            throw new \Exception('Not correct type');
        }

        $satisfaction[$requirement] = $value;
    }

    return $satisfaction;
}

// $requirement = new Requirement('key', 'string');
// $p = testme(
//     [
//         new Requirement('key', 'string'),
//     ],
//     ['key' => 'hello'],
// );

// print_r($p);


$p = new Satisfaction();
$p[10] = 'hello';
var_dump($p);
