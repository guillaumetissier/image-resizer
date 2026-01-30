<?php

namespace Guillaumetissier\ImageResizer\Validators;

use Guillaumetissier\ImageResizer\Constants\ImageType;
use Guillaumetissier\ImageResizer\Exceptions\InvalidPathException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\PathUtilities\Path;

final class SourceFileValidator implements ValidatorInterface
{
    /**
     * Validate that value is a readable file with supported format.
     *
     * @param mixed $value The path to validate
     *
     * @throws InvalidTypeException If $value is not a Path
     * @throws InvalidPathException If $value is not a readable file or has unsupported format
     */
    public static function validate(mixed $value): void
    {
        if (!$value instanceof Path) {
            throw InvalidTypeException::notPath('Source file', $value);
        }

        if (!$value->exists()) {
            throw InvalidPathException::notFound($value);
        }

        if (!$value->isFile()) {
            throw InvalidPathException::notFile($value);
        }

        if (!$value->permissions()->isReadable()) {
            throw InvalidPathException::notReadable($value);
        }

        if (!in_array($value->extension(), ImageType::allExtensions(), true)) {
            throw InvalidPathException::invalidFormat($value, ImageType::allExtensions());
        }
    }
}
