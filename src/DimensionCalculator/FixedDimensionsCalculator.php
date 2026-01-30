<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\EnumKeyedArray;
use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Exceptions\MissingKeyException;
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
     * @throws MissingKeyException
     * @throws InvalidTypeException
     */
    public function __construct(array $transformations)
    {
        $array = new EnumKeyedArray($transformations);

        $this->height = $array
            ->validateKeyExistence(Transformations::SET_HEIGHT)
            ->validateValueType(Transformations::SET_HEIGHT, 'int')
            ->value(Transformations::SET_HEIGHT);

        $this->width = $array
            ->validateKeyExistence(Transformations::SET_WIDTH)
            ->validateValueType(Transformations::SET_WIDTH, 'int')
            ->value(Transformations::SET_WIDTH);
    }

    public function calculateDimensions(ImageDimensions $originalDimensions): ImageDimensions
    {
        return new ImageDimensions($this->height, $this->width);
    }
}
