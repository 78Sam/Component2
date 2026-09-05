<?php

declare(strict_types=1);

namespace Core\Utility\Requirements\Services;

use Core\Utility\Requirements\AbstractRequirement;

class RequirementService
{
    /**
     * @param list<AbstractRequirement> $requirements
     * @param array<array-key, mixed> $provided
     */
    public static function validate(array $requirements, array $provided): void
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
}
