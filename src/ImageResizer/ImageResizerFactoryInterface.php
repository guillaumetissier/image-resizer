<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\ImageResizer;

use Guillaumetissier\PathUtilities\Path;

interface ImageResizerFactoryInterface
{
    public function create(Path $path, array $options): ImageResizerInterface;
}
