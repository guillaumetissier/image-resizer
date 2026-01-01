<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;

final class UnsupportedImageTypeException extends \Exception
{
    public function __construct(string $imageType, ?\Throwable $previous = null)
    {
        parent::__construct(
            "Unsupported type '$imageType'",
            ExceptionCodes::UNSUPPORTED_IMAGE_TYPE->value,
            $previous
        );
    }
}
