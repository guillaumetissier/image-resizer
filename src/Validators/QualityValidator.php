<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Validators;

use Guillaumetissier\ImageResizer\Constants\Quality;
use Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\ImageResizerConfig;

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
    private readonly int $minQuality;

    private readonly int $maxQuality;

    public function __construct(ImageResizerConfig $config)
    {
        $this->minQuality = $config->minQuality;
        $this->maxQuality = $config->maxQuality;
    }

    /**
     * Validate that quality is an integer and within acceptable range.
     *
     * @param mixed $value The quality value to validate
     *
     * @throws InvalidTypeException  If quality not int
     * @throws InvalidRangeException If quality is outside acceptable range
     */
    public function validate(mixed $value): void
    {
        if (!is_int($value)) {
            throw InvalidTypeException::notInt('quality', $value);
        }

        if ($value < $this->minQuality || $value > $this->maxQuality) {
            throw InvalidRangeException::outOfRange('quality', $value, $this->minQuality, $this->maxQuality);
        }
    }
}
