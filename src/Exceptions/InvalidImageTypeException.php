<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;

final class InvalidImageTypeException extends \Exception
{
    public static function invalidImageType(string $imageType, array $expected): self
    {
        return new self("Unsupported type '$imageType'. One of the following types expected: ".implode(', ', $expected));
    }

    private function __construct(string $message)
    {
        parent::__construct($message, ExceptionCodes::INVALID_IMAGE_TYPE->value);
    }
}
