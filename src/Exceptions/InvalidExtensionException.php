<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;
use Guillaumetissier\PathUtilities\Path;

final class InvalidExtensionException extends \RuntimeException
{
    public function __construct(Path $path, string $expectedExt, ?\Throwable $previous = null)
    {
        parent::__construct(
            "File has extension '{$path->extension()}'. '$expectedExt' is expected.",
            ExceptionCodes::INVALID_TARGET_EXTENSION->value,
            $previous
        );
    }
}
