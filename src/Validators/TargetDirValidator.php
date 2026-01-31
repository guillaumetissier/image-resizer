<?php

namespace Guillaumetissier\ImageResizer\Validators;

use Guillaumetissier\ImageResizer\Exceptions\InvalidPathException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\PathUtilities\Path;

final class TargetDirValidator implements ValidatorInterface
{
    /**
     * @throws InvalidTypeException
     */
    public function validate(mixed $value): void
    {
        if (!$value instanceof Path) {
            throw InvalidTypeException::notPath('Target dir', $value);
        }

        if (!$value->exists()) {
            throw InvalidPathException::notFound($value);
        }

        if (!$value->isDir()) {
            throw InvalidPathException::notDir($value);
        }

        if (!$value->permissions()->isWritable()) {
            throw InvalidPathException::notWritable($value);
        }
    }
}
