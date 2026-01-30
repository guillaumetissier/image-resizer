<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\DimensionReader;

use Guillaumetissier\ImageResizer\Exceptions\CannotReadImageSizeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidImageTypeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidPathException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use Guillaumetissier\PathUtilities\Path;

final class DimensionsReader implements DimensionsReaderInterface
{
    private const ALLOWED_EXTENSIONS = [
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
        IMAGETYPE_GIF,
    ];

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

        if (!in_array($infos[2], self::ALLOWED_EXTENSIONS, true)) {
            throw InvalidImageTypeException::invalidImageType($infos[2], self::ALLOWED_EXTENSIONS);
        }

        return new ImageDimensions($infos[1], $infos[0]);
    }
}
