<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Validators;

use Guillaumetissier\ImageResizer\Constants\Quality;
use Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;

/**
 * Validates image quality values.
 *
 * Quality is used for lossy compression formats like JPEG and WebP.
 * The quality value determines the balance between file size and image quality:
 * - Lower values (e.g., 0-50): Smaller file size, lower quality
 * - Medium values (e.g., 60-80): Good balance (recommended for web)
 * - Higher values (e.g., 85-100): Larger file size, higher quality
 *
 * Note: PNG is lossless and doesn't use quality settings.
 */
final class QualityValidator implements ValidatorInterface
{
    /**
     * Validate that quality is an integer and within acceptable range.
     *
     * @param mixed $value The quality value to validate
     *
     * @throws InvalidTypeException  If quality not int
     * @throws InvalidRangeException If quality is outside acceptable range
     */
    public static function validate(mixed $value): void
    {
        if (!is_int($value)) {
            throw InvalidTypeException::notInt('quality', $value);
        }

        if ($value < Quality::MIN->value || $value > Quality::MAX->value) {
            throw InvalidRangeException::outOfRange('quality', $value, Quality::MIN->value, Quality::MAX->value);
        }
    }
}
