<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;
use Guillaumetissier\PathUtilities\Path;

final class DirNotFoundException extends \RuntimeException
{
    public function __construct(Path $dir, ?\Throwable $previous = null)
    {
        parent::__construct(
            "Directory {$dir} not found",
            ExceptionCodes::DIR_NOT_FOUND->value,
            $previous
        );
    }
}
