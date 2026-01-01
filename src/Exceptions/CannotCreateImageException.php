<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;

final class CannotCreateImageException extends \Exception
{
    public function __construct(string $type, ?\Throwable $previous = null)
    {
        parent::__construct(
            "Cannot create image of type '$type'",
            ExceptionCodes::CANNOT_CREATE_IMAGE->value,
            $previous
        );
    }
}
