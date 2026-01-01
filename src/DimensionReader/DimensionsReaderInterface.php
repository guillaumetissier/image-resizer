<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionReader;

use Guillaumetissier\ImageResizer\ImageDimensions;
use Guillaumetissier\PathUtilities\Path;

interface DimensionsReaderInterface
{
    public function readDimensions(Path $file): ImageDimensions;
}
