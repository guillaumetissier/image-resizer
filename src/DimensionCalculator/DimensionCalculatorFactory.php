<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\ResizeType;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;

final class DimensionCalculatorFactory implements DimensionCalculatorFactoryInterface
{
    /**
     * @param array{
     *      setHeight?: int,
     *      setWidth?: int,
     *      setRatio?: int|float
     *  } $transformations
     *
     * @throws InvalidTypeException
     */
    public function create(ResizeType $resizeType, array $transformations): DimensionCalculatorInterface
    {
        return match ($resizeType) {
            ResizeType::PROPORTIONAL => new ProportionalDimensionsCalculator($transformations),
            ResizeType::FIXED => new FixedDimensionsCalculator($transformations),
            ResizeType::FIXED_HEIGHT => new FixedHeightCalculator($transformations),
            ResizeType::FIXED_WIDTH => new FixedWidthCalculator($transformations),
        };
    }
}
