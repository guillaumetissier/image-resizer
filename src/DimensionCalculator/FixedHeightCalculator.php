<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\Exceptions\MissingTransformationException;
use Guillaumetissier\ImageResizer\Exceptions\WrongValueTypeException;
use Guillaumetissier\ImageResizer\ImageDimensions;

final class FixedHeightCalculator implements DimensionCalculatorInterface
{
    private int $height;

    /**
     * @param array{
     *     setHeight: int
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

        if (!is_int($transformations[Transformations::SET_HEIGHT->value])) {
            throw new WrongValueTypeException(Transformations::SET_HEIGHT->value, 'integer');
        }

        $this->height = $transformations[Transformations::SET_HEIGHT->value];
    }

    public function calculateDimensions(ImageDimensions $originalDimensions): ImageDimensions
    {
        return new ImageDimensions(
            $this->height,
            (int) round($this->height * $originalDimensions->getWidth() / $originalDimensions->getHeight())
        );
    }
}
