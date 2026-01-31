<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\ImageResizer;

use Guillaumetissier\ImageResizer\Constants\Options;
use Guillaumetissier\ImageResizer\Exceptions\CannotCreateImageException;
use Guillaumetissier\ImageResizer\Exceptions\CannotSaveImageException;

final class PngImageResizer extends AbstractImageResizer
{
    /**
     * @throws CannotCreateImageException
     */
    protected function setSource(string $source): void
    {
        if (false === ($temp = @imagecreatefrompng($source))) {
            throw new CannotCreateImageException('png');
        }

        $this->source = $temp;
    }

    /**
     * @throws CannotSaveImageException
     */
    protected function save(string $target): void
    {
        // Compression level: from 0 (no compression) to 9.
        $quality = $this->getOption(Options::QUALITY, $this->config->defaultQuality);
        $quality = (int) round((100 - $quality) / 10);

        if (false === @imagepng($this->target, $target, $quality)) {
            throw new CannotSaveImageException($target);
        }
    }
}
