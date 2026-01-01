<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\Exceptions\MissingTransformationException;
use Guillaumetissier\ImageResizer\Exceptions\WrongValueTypeException;
use Guillaumetissier\ImageResizer\ImageDimensions;

final class FixedWidthCalculator implements DimensionCalculatorInterface
{
    private int $width;

    /**
     * @param array{
     *     setWidth: int
     * } $transformations
     *
     * @throws MissingTransformationException
     * @throws WrongValueTypeException
     */
    public function __construct(array $transformations)
    {
        if (!isset($transformations[Transformations::SET_WIDTH->value])) {
            throw new MissingTransformationException(Transformations::SET_WIDTH);
        }

        if (!is_int($transformations[Transformations::SET_WIDTH->value])) {
            throw new WrongValueTypeException(Transformations::SET_WIDTH->value, 'integer');
        }

        $this->width = $transformations[Transformations::SET_WIDTH->value];
    }

    public function calculateDimensions(ImageDimensions $originalDimensions): ImageDimensions
    {
        return new ImageDimensions(
            (int) round($this->width * $originalDimensions->getHeight() / $originalDimensions->getWidth()),
            $this->width
        );
    }
}
