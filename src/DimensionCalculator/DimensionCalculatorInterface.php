<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionCalculator;

use Guillaumetissier\ImageResizer\ImageDimensions;

interface DimensionCalculatorInterface
{
    public function calculateDimensions(ImageDimensions $originalDimensions): ImageDimensions;
}
