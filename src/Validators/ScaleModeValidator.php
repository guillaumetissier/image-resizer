<?php

namespace Guillaumetissier\ImageResizer\Validators;

use Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;

class ScaleModeValidator implements ValidatorInterface
{
    private const ALLOWED = [IMG_NEAREST_NEIGHBOUR, IMG_BILINEAR_FIXED, IMG_BICUBIC, IMG_BICUBIC_FIXED];

    public function validate(mixed $value): void
    {
        if (!is_int($value)) {
            throw InvalidTypeException::notInt('scale mode', $value);
        }

        if (!in_array($value, self::ALLOWED)) {
            throw InvalidRangeException::outOfSet('scale mode', $value, self::ALLOWED);
        }
    }
}
