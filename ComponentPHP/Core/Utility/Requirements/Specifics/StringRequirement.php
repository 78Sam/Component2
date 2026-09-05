<?php

declare(strict_types=1);

namespace Core\Utility\Requirements\Specifics;

use Core\Utility\Requirements\AbstractRequirement;
use Core\Utility\Requirements\Exceptions\ValidationException;

/**
 * @extends AbstractRequirement<string|ValidationException>
 */
class StringRequirement extends AbstractRequirement
{
    #[\Override]
    public function validate(mixed $value): void
    {
        if (!is_string($value))
        {
            $this->value = new ValidationException('Value must be a string');

            return;
        }

        $this->value = $value;
    }
}
