<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\ImageResizer;

use Guillaumetissier\ImageResizer\ImageDimensions;
use Guillaumetissier\PathUtilities\Path;

interface ImageResizerInterface
{
    public function resize(Path $source, Path $target, ImageDimensions $newDimensions): void;
}
