<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;
use Guillaumetissier\PathUtilities\Path;

final class CannotReadImageSizeException extends \RuntimeException
{
    public function __construct(Path $file, ?\Throwable $previous = null)
    {
        parent::__construct(
            "Cannot read sizes of $file.",
            ExceptionCodes::CANNOT_READ_IMAGE_SIZE->value,
            $previous
        );
    }
}
