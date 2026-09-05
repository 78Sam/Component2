<?php

declare(strict_types=1);

namespace Core\Utility\Requirements\Specifics;

use Core\Utility\Requirements\AbstractRequirement;
use Core\Utility\Requirements\Exceptions\ValidationException;

/**
 * @extends AbstractRequirement<int|ValidationException>
 */
class IntOrStringIntRequirement extends AbstractRequirement
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
            $this->value = new ValidationException('Value must be an integer or string integer');

            return;
        }

        $this->value = $value;
    }
}
