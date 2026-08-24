<?php

declare(strict_types=1);

namespace Core\Utility\Resolvers;

use Core\Utility\Resolvers\Exceptions\ResolveException;

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

    private function resolveString(mixed $value): ?string
    {
        $type = gettype($value);
        if ($type !== 'string')
        {
            $this->error(new ResolveException($value, $type, 'string'));

            return null;
        }

        return $value;
    }

    private function resolveInt(mixed $value): ?int
    {
        $type = gettype($value);
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


        // if ($type === 'string' && ctype_digit($value))
        // {
        //     $value = (int) $value;
        //     $type = gettype($value);
        // }

        // if ($type !== 'integer')
        // {
        //     $this->error(new ResolveException($value, $type, 'integer'));

        //     return null;
        // }

        return $value;
    }
}
