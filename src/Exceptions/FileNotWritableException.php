<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;
use Guillaumetissier\PathUtilities\Path;

final class FileNotWritableException extends \RuntimeException
{
    public function __construct(Path $file, ?\Throwable $previous = null)
    {
        parent::__construct(
            "File {$file->absolutePath()} not writable",
            ExceptionCodes::FILE_NOT_WRITABLE->value,
            $previous
        );
    }
}
