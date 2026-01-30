<?php

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;

final class MissingKeyException extends \LogicException
{
    public static function missingKey(\BackedEnum $key): self
    {
        return new self("Key '{$key->value}' is missing.");
    }

    private function __construct(string $message)
    {
        parent::__construct($message, ExceptionCodes::MISSING_KEY->value);
    }
}
