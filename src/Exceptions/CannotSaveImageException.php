<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;

final class CannotSaveImageException extends \Exception
{
    public function __construct(string $file, ?\Throwable $previous = null)
    {
        parent::__construct(
            "Cannot save image '$file'",
            ExceptionCodes::CANNOT_SAVE_IMAGE->value,
            $previous
        );
    }
}
