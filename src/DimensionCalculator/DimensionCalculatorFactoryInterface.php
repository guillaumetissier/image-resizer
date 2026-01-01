<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\ResizeType;

interface DimensionCalculatorFactoryInterface
{
    public function create(ResizeType $resizeType, array $transformations): DimensionCalculatorInterface;
}
