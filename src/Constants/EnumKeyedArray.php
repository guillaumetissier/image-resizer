<?php

namespace Guillaumetissier\ImageResizer\Constants;

use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Exceptions\MissingKeyException;

final class EnumKeyedArray
{
    public function __construct(private readonly array $array)
    {
    }

    public function value(\BackedEnum $key): mixed
    {
        return $this->array[$key->value] ?? null;
    }

    /**
     * Check if key exists in array.
     */
    public function keyExists(\BackedEnum $key): bool
    {
        return array_key_exists($key->value, $this->array);
    }

    /**
     * Validate that the key exists.
     *
     * @throws MissingKeyException
     */
    public function validateKeyExistence(\BackedEnum $key): self
    {
        if (!$this->keyExists($key)) {
            throw MissingKeyException::missingKey($key);
        }

        return $this;
    }

    /**
     * Validate type of value in array.
     *
     * @throws InvalidTypeException
     */
    public function validateValueType(\BackedEnum $key, string $expectedType, bool $nullable = false): self
    {
        $value = $this->value($key);

        if (null === $value && $nullable) {
            return $this;
        }

        if ('int' === $expectedType && !is_int($value)) {
            throw InvalidTypeException::notInt($key->value, $value);
        }

        if ('float' === $expectedType && !is_float($value) && !is_int($value)) {
            throw InvalidTypeException::notFloat($key->value, $value);
        }

        return $this;
    }
}
