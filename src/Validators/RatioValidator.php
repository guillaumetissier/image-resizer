<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Validators;

use Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\ImageResizerConfig;

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
    private readonly float $minRatio;

    private readonly float $maxRatio;

    public function __construct(ImageResizerConfig $config)
    {
        $this->minRatio = $config->minRatio;
        $this->maxRatio = $config->maxRatio;
    }

    /**
     * Validate that ratio is within acceptable range.
     *
     * @param int|float $value The ratio to validate
     *
     * @throws InvalidTypeException  If ratio is not numeric
     * @throws InvalidRangeException If ratio is outside acceptable range
     */
    public function validate(mixed $value): void
    {
        if (!is_float($value) && !is_int($value)) {
            throw InvalidTypeException::notFloat('ratio', $value);
        }

        if ($value < $this->minRatio || $value > $this->maxRatio) {
            throw InvalidRangeException::outOfRange('ratio', $value, $this->minRatio, $this->maxRatio);
        }
    }
}
