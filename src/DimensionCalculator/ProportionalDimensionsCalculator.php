<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\EnumKeyedArray;
use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Exceptions\MissingKeyException;
use Guillaumetissier\ImageResizer\ImageDimensions;

final class ProportionalDimensionsCalculator implements DimensionCalculatorInterface
{
    private float $ratio;

    /**
     * @param array{
     *   setRatio: float|int
     * } $transformations
     *
     * @throws MissingKeyException
     * @throws InvalidTypeException
     */
    public function __construct(array $transformations)
    {
        $array = new EnumKeyedArray($transformations);

        if (!$array->keyExists(Transformations::SET_RATIO)) {
            throw MissingKeyException::missingKey(Transformations::SET_RATIO);
        }

        $this->ratio = floatval($array
            ->validateValueType(Transformations::SET_RATIO, 'float')
            ->value(Transformations::SET_RATIO)
        );
    }

    public function calculateDimensions(ImageDimensions $originalDimensions): ImageDimensions
    {
        return new ImageDimensions(
            (int) round($this->ratio * $originalDimensions->getHeight()),
            (int) round($this->ratio * $originalDimensions->getWidth()),
        );
    }
}
