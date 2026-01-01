<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;

final class WrongValueTypeException extends \Exception
{
    public function __construct(string $key, string $expectedType, ?\Throwable $previous = null)
    {
        parent::__construct(
            "Wrong value for '$key'. $expectedType is expected.",
            ExceptionCodes::WRONG_VALUE_TYPE->value,
            $previous
        );
    }
}
