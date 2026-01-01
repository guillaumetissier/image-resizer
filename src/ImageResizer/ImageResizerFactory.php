<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\ImageResizer;

use Guillaumetissier\ImageResizer\Exceptions\UnsupportedImageTypeException;
use Guillaumetissier\PathUtilities\Path;

final class ImageResizerFactory implements ImageResizerFactoryInterface
{
    /**
     * @param array{
     *     mode?: string,
     *     interlace?: bool,
     *     quality?: int,
     * } $options
     *
     * @throws UnsupportedImageTypeException
     */
    public function create(Path $path, array $options): ImageResizerInterface
    {
        return match ($ext = $path->extension()) {
            'gif' => new GifImageResizer($options),
            'jpeg', 'jpg' => new JpegImageResizer($options),
            'png' => new PngImageResizer($options),
            '' => throw new UnsupportedImageTypeException('unknown'),
            default => throw new UnsupportedImageTypeException($ext),
        };
    }
}
