<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\EnumKeyedArray;
use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Exceptions\MissingKeyException;
use Guillaumetissier\ImageResizer\ImageDimensions;

final class FixedWidthCalculator implements DimensionCalculatorInterface
{
    private int $width;

    /**
     * @param array{
     *     setWidth: int
     * } $transformations
     *
     * @throws MissingKeyException
     * @throws InvalidTypeException
     */
    public function __construct(array $transformations)
    {
        $array = new EnumKeyedArray($transformations);

        $this->width = $array
            ->validateKeyExistence(Transformations::SET_WIDTH)
            ->validateValueType(Transformations::SET_WIDTH, 'int')
            ->value(Transformations::SET_WIDTH);
    }

    public function calculateDimensions(ImageDimensions $originalDimensions): ImageDimensions
    {
        return new ImageDimensions(
            (int) round($this->width * $originalDimensions->getHeight() / $originalDimensions->getWidth()),
            $this->width
        );
    }
}
