<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\EnumKeyedArray;
use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Exceptions\MissingKeyException;
use Guillaumetissier\ImageResizer\ImageDimensions;

final class FixedHeightCalculator implements DimensionCalculatorInterface
{
    private int $height;

    /**
     * @param array<Transformations, int|float> $transformations Transformations indexed by enum
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
    }

    public function calculateDimensions(ImageDimensions $originalDimensions): ImageDimensions
    {
        return new ImageDimensions(
            $this->height,
            (int) round($this->height * $originalDimensions->getWidth() / $originalDimensions->getHeight())
        );
    }
}
