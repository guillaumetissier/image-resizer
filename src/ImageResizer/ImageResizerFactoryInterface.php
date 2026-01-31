<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\ImageResizer;

use Guillaumetissier\ImageResizer\ImageResizerConfig;
use Guillaumetissier\PathUtilities\Path;

interface ImageResizerFactoryInterface
{
    public function create(Path $path, ImageResizerConfig $config, array $options): ImageResizerInterface;
}
