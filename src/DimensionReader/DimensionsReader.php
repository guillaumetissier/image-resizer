<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionReader;

use Guillaumetissier\ImageResizer\Constants\ImageType;
use Guillaumetissier\ImageResizer\Exceptions\CannotReadImageSizeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidImageTypeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidPathException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use Guillaumetissier\PathUtilities\Path;

final class DimensionsReader implements DimensionsReaderInterface
{
    /**
     * @throws InvalidPathException|CannotReadImageSizeException
     * @throws InvalidImageTypeException
     */
    public function readDimensions(Path $file): ImageDimensions
    {
        if (!$file->isFile()) {
            throw InvalidPathException::notFile($file);
        }

        if (false === ($infos = @getimagesize((string) $file))) {
            throw new CannotReadImageSizeException($file);
        }

        if (!in_array($infos[2], ImageType::all(), true)) {
            throw InvalidImageTypeException::invalidImageType(ImageType::toExtension($infos[2]), ImageType::allExtensions());
        }

        return new ImageDimensions($infos[1], $infos[0]);
    }
}
