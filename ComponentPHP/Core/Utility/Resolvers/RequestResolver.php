<?php

declare(strict_types=1);

namespace ComponentPHP\Utility\Resolvers;

use ComponentPHP\Utility\Resolvers\Exceptions\ResolveException;

class RequestResolver extends AbstractResolver
{
    #[\Override]
    protected function getResolvers(): array
    {
        return [
            'string' => $this->resolveString(...),
            'int' => $this->resolveInt(...),
        ];
    }

    // TODO: TBH these resolvers are a bit weak, need working on, what are gonna be failed return values for example

    private function resolveString(mixed $value, string $type): string
    {
        if ($type !== 'string')
        {
            $this->error(new ResolveException($value, $type, 'string'));

            return '';
        }

        return $value;
    }

    private function resolveInt(mixed $value, string $type): int
    {
        if ($type === 'string' && ctype_digit($value))
        {
            $value = (int) $value;
            $type = gettype($value);
        }

        if ($type !== 'integer')
        {
            $this->error(new ResolveException($value, $type, 'integer'));

            return -1;
        }

        return $value;
    }
}
