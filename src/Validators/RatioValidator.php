<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Validators;

use Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;

/**
 * Validates image ratio values.
 *
 * A ratio represents the scaling factor applied to an image.
 * For example:
 * - 0.5 = 50% of original size (shrink)
 * - 1.0 = 100% of original size (no change)
 * - 2.0 = 200% of original size (enlarge)
 */
final class RatioValidator implements ValidatorInterface
{
    private const MIN_RATIO = 0.01;  // 1% minimum (prevent too small images)
    private const MAX_RATIO = 2.0;  // 200% maximum (prevent memory issues)

    /**
     * Validate that ratio is within acceptable range.
     *
     * @param int|float $value The ratio to validate
     *
     * @throws InvalidTypeException  If ratio is not numeric
     * @throws InvalidRangeException If ratio is outside acceptable range
     */
    public static function validate(mixed $value): void
    {
        if (!is_float($value) && !is_int($value)) {
            throw InvalidTypeException::notFloat('ratio', $value);
        }

        if ($value < self::MIN_RATIO || $value > self::MAX_RATIO) {
            throw InvalidRangeException::outOfRange('ratio', $value, self::MIN_RATIO, self::MAX_RATIO);
        }
    }
}
