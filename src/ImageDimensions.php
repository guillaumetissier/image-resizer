<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer;

final class ImageDimensions
{
    public function __construct(private readonly int $height, private readonly int $width)
    {
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getWidth(): int
    {
        return $this->width;
    }
}
