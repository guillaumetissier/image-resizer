<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Validators;

use Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;

class BaseSizeValidator implements ValidatorInterface
{
    public function __construct(
        private readonly string $dimension,
        private readonly int $min,
        private readonly int $max,
    ) {
    }

    /**
     * Validate that dimension is an integer within acceptable range.
     *
     * @throws InvalidTypeException
     * @throws InvalidRangeException
     */
    public function validate(mixed $value): void
    {
        if (!is_int($value)) {
            throw InvalidTypeException::notInt($this->dimension, $value);
        }

        if ($value < $this->min || $value > $this->max) {
            throw InvalidRangeException::outOfRange($this->dimension, $value, $this->min, $this->max);
        }
    }
}
