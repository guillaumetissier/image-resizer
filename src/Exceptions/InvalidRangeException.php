<?php

namespace Guillaumetissier\ImageResizer\Exceptions;

use Guillaumetissier\ImageResizer\Constants\ExceptionCodes;

/**
 * Exception thrown when a value out of a range is provided.
 */
final class InvalidRangeException extends \InvalidArgumentException
{
    /**
     * Create exception for a value that is not in a set of allowed values.
     *
     * @param string    $variable The name of the variable whose value is invalid
     * @param int|float $value    The invalid value
     * @param array     $values   The allowed values
     */
    public static function outOfSet(string $variable, int|float $value, array $values): self
    {
        return new self(sprintf("$variable expected to be in (%s). Got $value.", implode(', ', $values)));
    }

    /**
     * Create exception for a dimension value that is too small.
     *
     * @param string    $variable The name of the variable whose value is invalid
     * @param int|float $value    The invalid value
     * @param int|float $min      The minimum allowed value
     * @param int|float $max      The maximum allowed value
     */
    public static function outOfRange(string $variable, int|float $value, int|float $min, int|float $max): self
    {
        return new self("$variable expected to be in [$min, $max]. Got $value.");
    }

    /**
     * Private constructor to force usage of named constructors.
     */
    private function __construct(string $message)
    {
        parent::__construct($message, ExceptionCodes::INVALID_RANGE->value);
    }
}
