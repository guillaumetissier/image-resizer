<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;
use Guillaumetissier\PathUtilities\Path;

final class InvalidPathException extends \RuntimeException
{
    /**
     * Create exception if path cannot be found.
     *
     * @param Path $path The invalid path value
     */
    public static function notFound(Path $path): self
    {
        return new self("Path $path not found.");
    }

    /**
     * Create exception if path is not a directory.
     *
     * @param Path $path The invalid path value
     */
    public static function notDir(Path $path): self
    {
        return new self("Path $path not a directory.");
    }

    /**
     * Create exception if path is not a file.
     *
     * @param Path $path The invalid path value
     */
    public static function notFile(Path $path): self
    {
        return new self("Path $path not a file.");
    }

    /**
     * Create exception if path has invalid format.
     *
     * @param Path            $path     The invalid path value
     * @param string[]|string $expected The expected format(s)
     */
    public static function invalidFormat(Path $path, array|string $expected): self
    {
        if (is_string($expected)) {
            $expected = [$expected];
        }

        return new self(sprintf("Path $path has invalid format. Expected formats: %s", join(', ', $expected)));
    }

    /**
     * Create exception if path is not readable.
     *
     * @param Path $path The invalid path value
     */
    public static function notReadable(Path $path): self
    {
        return new self("Path $path not readable.");
    }

    /**
     * Create exception if path is not writable.
     *
     * @param Path $path The invalid path value
     */
    public static function notWritable(Path $path): self
    {
        return new self("Path $path not writable.");
    }

    /**
     * Private constructor to force usage of named constructors.
     */
    private function __construct(string $error)
    {
        parent::__construct($error, ExceptionCodes::INVALID_PATH->value);
    }
}
