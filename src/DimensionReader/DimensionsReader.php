<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionReader;

use Guillaumetissier\ImageResizer\Exceptions\CannotReadImageSizeException;
use Guillaumetissier\ImageResizer\Exceptions\FileNotFoundException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use Guillaumetissier\PathUtilities\Path;

final class DimensionsReader implements DimensionsReaderInterface
{
    /**
     * @throws FileNotFoundException|CannotReadImageSizeException
     */
    public function readDimensions(Path $file): ImageDimensions
    {
        if (!$file->isFile()) {
            throw new FileNotFoundException($file);
        }

        if (false === ($infos = @getimagesize((string) $file))) {
            throw new CannotReadImageSizeException($file);
        }

        return new ImageDimensions($infos[1], $infos[0]);
    }
}
