<?php

declare(strict_types=1);

namespace Core\Utility\Validators\Types;

use Core\Utility\Validators\Exceptions\ValidationException;

/**
 * @extends AbstractValidator<string|ValidationException>
 */
class StringValidator extends AbstractValidator
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
