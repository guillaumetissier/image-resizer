<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\Exceptions\MissingTransformationException;
use Guillaumetissier\ImageResizer\Exceptions\WrongValueTypeException;
use Guillaumetissier\ImageResizer\ImageDimensions;

final class ProportionalDimensionsCalculator implements DimensionCalculatorInterface
{
    private float $ratio;

    /**
     * @param array{
     *   setRatio: float|int
     * } $transformations
     *
     * @throws MissingTransformationException
     * @throws WrongValueTypeException
     */
    public function __construct(array $transformations)
    {
        if (!isset($transformations[Transformations::SET_RATIO->value])) {
            throw new MissingTransformationException(Transformations::SET_RATIO);
        }

        if (!is_numeric($transformations[Transformations::SET_RATIO->value])) {
            throw new WrongValueTypeException(Transformations::SET_RATIO->value, 'numeric');
        }

        $this->ratio = floatval($transformations[Transformations::SET_RATIO->value]);
    }

    public function calculateDimensions(ImageDimensions $originalDimensions): ImageDimensions
    {
        return new ImageDimensions(
            (int) round($this->ratio * $originalDimensions->getHeight()),
            (int) round($this->ratio * $originalDimensions->getWidth()),
        );
    }
}
