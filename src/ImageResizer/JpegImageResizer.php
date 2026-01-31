<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\ImageResizer;

use Guillaumetissier\ImageResizer\Constants\Options;
use Guillaumetissier\ImageResizer\Exceptions\CannotCreateImageException;
use Guillaumetissier\ImageResizer\Exceptions\CannotSaveImageException;

final class JpegImageResizer extends AbstractImageResizer
{
    /**
     * @throws CannotCreateImageException
     */
    protected function setSource(string $source): void
    {
        if (false === ($temp = @imagecreatefromjpeg($source))) {
            throw new CannotCreateImageException('jpg');
        }

        $this->source = $temp;
    }

    /**
     * @throws CannotSaveImageException
     */
    protected function save(string $target): void
    {
        $quality = $this->getOption(Options::QUALITY, $this->config->defaultQuality);

        if (false === @imagejpeg($this->target, $target, $quality)) {
            throw new CannotSaveImageException($target);
        }
    }
}
