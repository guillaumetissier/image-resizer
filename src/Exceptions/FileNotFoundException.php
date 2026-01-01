<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;
use Guillaumetissier\PathUtilities\Path;

final class FileNotFoundException extends \RuntimeException
{
    public function __construct(Path $file, ?\Throwable $previous = null)
    {
        parent::__construct(
            "File {$file} not found",
            ExceptionCodes::FILE_NOT_FOUND->value,
            $previous
        );
    }
}
