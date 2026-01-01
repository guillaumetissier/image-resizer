<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;
use Guillaumetissier\PathUtilities\Path;

final class DirNotWritableException extends \RuntimeException
{
    public function __construct(Path $dir, ?\Throwable $previous = null)
    {
        parent::__construct(
            "Directory {$dir->absolutePath()} not writable",
            ExceptionCodes::DIR_NOT_FOUND->value,
            $previous
        );
    }
}
