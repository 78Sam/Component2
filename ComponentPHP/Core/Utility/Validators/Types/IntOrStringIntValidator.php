<?php

declare(strict_types=1);

namespace Core\Utility\Validators\Types;

use Core\Utility\Validators\Exceptions\ValidationException;

/**
 * @extends AbstractValidator<int|ValidationException>
 */
class IntOrStringIntValidator extends AbstractValidator
{
    #[\Override]
    public function validate(mixed $value): void
    {
        if (is_string($value)) {
            $isNegative = false;
            if (substr($value, 0, 1) === '-') {
                $isNegative = true;
                $value = substr($value, 1);
            }

            if (ctype_digit($value)) {
                $value = (int) $value;
                if ($isNegative) {
                    $value = -$value;
                }
            }
        }

        if (!is_int($value)) {
            $this->value = new ValidationException('Value must be an integer or string integer');

            return;
        }

        $this->value = $value;
    }
}
