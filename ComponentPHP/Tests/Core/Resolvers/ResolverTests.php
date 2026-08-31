<?php

declare(strict_types=1);

namespace Tests\Core\Resolvers;

use Core\Testing\AbstractTest;
use Core\Testing\Attributes\Test;
use Core\Utility\Resolvers\RequestResolver;

class ResolverTests extends AbstractTest
{
    private RequestResolver $requestResolver;

    #[\Override]
    public function setup(): void {
        $this->requestResolver = new RequestResolver(throwErrors: false);
    }

    #[\Override]
    public function teardown(): void {}

    #[Test('Test a required string')]
    public function stringTest(): void
    {
        $sampleData = ['key' => 'some data'];
        $result = $this->requestResolver->resolve(
            ['key' => 'string'],
            $sampleData,
        );
        static::assertEquals($result, $sampleData, 'Failed to resolve a string');
    }

    #[Test('Test a required int')]
    public function intTest(): void
    {
        $sampleData = ['key' => 12];
        $result = $this->requestResolver->resolve(
            ['key' => 'int'],
            $sampleData,
        );
        static::assertEquals($result, $sampleData, 'Failed to resolve an int');
    }

    #[Test('Test a string int')]
    public function stringIntTest(): void
    {
        $result = $this->requestResolver->resolve(
            ['key' => 'int'],
            ['key' => '-1'],
        );
        static::assertEquals($result, ['key' => -1], 'Failed to resolve a string int');
    }

    #[Test('Test undefined key removal')]
    public function undefinedKeyRemovalTest(): void
    {
        $result = $this->requestResolver->resolve(
            ['key' => 'int'],
            [
                'key' => '-1',
                'undefinedKey' => 'remove me',
            ],
        );
        static::assertEquals($result, ['key' => -1], 'Failed to resolve a string int');
    }

    #[Test('Test nullable keys')]
    public function nullableKeyTest(): void
    {
        $result = $this->requestResolver->resolve(
            ['key' => '?int'],
            [],
        );
        static::assertEquals($result, ['key' => null], 'Failed to resolve a nullable key');
    }
}
