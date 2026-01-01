<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\Exceptions\MissingTransformationException;
use Guillaumetissier\ImageResizer\Exceptions\WrongValueTypeException;
use Guillaumetissier\ImageResizer\ImageDimensions;

final class FixedDimensionsCalculator implements DimensionCalculatorInterface
{
    private int $height;

    private int $width;

    /**
     * @param array{
     *   setWidth: int,
     *   setHeight: int
     * } $transformations
     *
     * @throws MissingTransformationException
     * @throws WrongValueTypeException
     */
    public function __construct(array $transformations)
    {
        if (!isset($transformations[Transformations::SET_HEIGHT->value])) {
            throw new MissingTransformationException(Transformations::SET_HEIGHT);
        }

        if (!isset($transformations[Transformations::SET_WIDTH->value])) {
            throw new MissingTransformationException(Transformations::SET_WIDTH);
        }

        if (!is_int($transformations[Transformations::SET_HEIGHT->value])) {
            throw new WrongValueTypeException(Transformations::SET_HEIGHT->value, 'integer');
        }

        if (!is_int($transformations[Transformations::SET_WIDTH->value])) {
            throw new WrongValueTypeException(Transformations::SET_WIDTH->value, 'integer');
        }

        $this->height = $transformations[Transformations::SET_HEIGHT->value];
        $this->width = $transformations[Transformations::SET_WIDTH->value];
    }

    public function calculateDimensions(ImageDimensions $originalDimensions): ImageDimensions
    {
        return new ImageDimensions($this->height, $this->width);
    }
}
