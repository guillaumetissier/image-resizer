<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Validators;

use Guillaumetissier\ImageResizer\Constants\Quality;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;

/**
 * Validates interlace values.
 */
final class InterlaceValidator implements ValidatorInterface
{
    /**
     * Validate that interlace is a boolean.
     *
     * @param mixed $value The quality value to validate
     *
     * @throws InvalidTypeException If interlace not boolean
     */
    public function validate(mixed $value): void
    {
        if (!is_bool($value)) {
            throw InvalidTypeException::notInt('interlace', $value);
        }
    }
}
