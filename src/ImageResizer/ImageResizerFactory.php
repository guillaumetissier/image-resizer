<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\ImageResizer;

use Guillaumetissier\ImageResizer\Constants\ImageType;
use Guillaumetissier\ImageResizer\Exceptions\InvalidImageTypeException;
use Guillaumetissier\ImageResizer\ImageResizerConfig;
use Guillaumetissier\PathUtilities\Path;

final class ImageResizerFactory implements ImageResizerFactoryInterface
{
    /**
     * Create an image resizer for the given file path.
     *
     * @param Path $path The image file path
     * @param array{
     *     quality?: int,
     *     interlace?: bool
     * } $options Resizer options
     *
     * @throws InvalidImageTypeException If image format is not supported
     */
    public function create(Path $path, ImageResizerConfig $config, array $options): ImageResizerInterface
    {
        return match ($ext = $path->extension()) {
            'gif' => new GifImageResizer($config, $options),
            'jpeg', 'jpg' => new JpegImageResizer($config, $options),
            'png' => new PngImageResizer($config, $options),
            default => throw InvalidImageTypeException::invalidImageType($ext, ImageType::allExtensions()),
        };
    }
}
