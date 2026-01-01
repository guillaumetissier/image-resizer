<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;
use Guillaumetissier\ImageResizer\Constants\Transformations;

final class MissingTransformationException extends \Exception
{
    public function __construct(Transformations $key, ?\Throwable $previous = null)
    {
        parent::__construct(
            "Transformation key '{$key->value}' is missing.",
            ExceptionCodes::MISSING_TRANSFORMATION_KEY->value,
            $previous
        );
    }
}
