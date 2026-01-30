<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Validators;

use Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;

final class DimensionValidator implements ValidatorInterface
{
    private const MIN_DIMENSION = 10;
    private const MAX_DIMENSION = 2000;

    /**
     * Validate that dimension is an integer within acceptable range.
     *
     * @throws InvalidTypeException
     * @throws InvalidRangeException
     */
    public static function validate(mixed $value): void
    {
        if (!is_int($value)) {
            throw InvalidTypeException::notInt('dimension', $value);
        }

        if ($value < self::MIN_DIMENSION || $value > self::MAX_DIMENSION) {
            throw InvalidRangeException::outOfRange('dimension', $value, self::MIN_DIMENSION, self::MAX_DIMENSION);
        }
    }
}
