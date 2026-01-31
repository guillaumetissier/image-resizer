<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;

final class InvalidTypeException extends \Exception
{
    /**
     * Create exception for a variable whose value is not a boolean.
     *
     * @param string $variable The name of the variable with invalid type
     * @param mixed  $value    The invalid value
     */
    public static function notBool(string $variable, mixed $value): self
    {
        return new self($variable, 'boolean', $value);
    }

    /**
     * Create exception for a variable whose value is not an integer.
     *
     * @param string $variable The name of the variable with invalid type
     * @param mixed  $value    The invalid value
     */
    public static function notInt(string $variable, mixed $value): self
    {
        return new self($variable, 'integer', $value);
    }

    /**
     * Create exception for a variable whose value is not a float.
     *
     * @param string $variable The name of the variable with invalid type
     * @param mixed  $value    The invalid value
     */
    public static function notFloat(string $variable, mixed $value): self
    {
        return new self($variable, 'float', $value);
    }

    /**
     * Create exception for a variable whose value is not a Path.
     *
     * @param string $variable The name of the variable with invalid type
     * @param mixed  $value    The invalid value
     */
    public static function notPath(string $variable, mixed $value): self
    {
        return new self($variable, 'Path', $value);
    }

    /**
     * Create exception for a variable whose value is not a ImageDimensions.
     *
     * @param string $variable The name of the variable with invalid type
     * @param mixed  $value    The invalid value
     */
    public static function notImageDimensions(string $variable, mixed $value): self
    {
        return new self($variable, 'ImageDimensions', $value);
    }

    private function __construct(string $variable, string $expectedType, mixed $value)
    {
        parent::__construct(
            "$variable must be a $expectedType, got: ".gettype($value),
            ExceptionCodes::INVALID_TYPE->value
        );
    }
}
