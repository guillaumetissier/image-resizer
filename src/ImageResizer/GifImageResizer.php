<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\ImageResizer;

use Guillaumetissier\ImageResizer\Exceptions\CannotCreateImageException;
use Guillaumetissier\ImageResizer\Exceptions\CannotSaveImageException;

final class GifImageResizer extends AbstractImageResizer
{
    /**
     * @throws CannotCreateImageException
     */
    protected function setSource(string $source): void
    {
        if (false === ($temp = @imagecreatefromgif($source))) {
            throw new CannotCreateImageException('gif');
        }

        $this->source = $temp;
    }

    /**
     * @throws CannotSaveImageException
     */
    protected function save(string $target): void
    {
        if (false === @imagegif($this->target, $target)) {
            throw new CannotSaveImageException($target);
        }
    }
}
