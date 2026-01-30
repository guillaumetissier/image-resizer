<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\Validators;

use Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Validators\DimensionValidator;
use PHPUnit\Framework\TestCase;

final class DimensionValidatorTest extends TestCase
{
    /**
     * @dataProvider validDimensionProvider
     */
    public function testValidateAcceptsValidDimensions(int $dimension): void
    {
        DimensionValidator::validate($dimension);

        $this->assertTrue(true);
    }

    public static function validDimensionProvider(): array
    {
        return [
            'minimum boundary' => [10],
            'just above minimum' => [11],
            'small dimension' => [50],
            'medium dimension' => [500],
            'large dimension' => [1500],
            'just below maximum' => [1999],
            'maximum boundary' => [2000],
        ];
    }

    /**
     * @dataProvider invalidTypeProvider
     */
    public function testValidateThrowsForInvalidTypes(mixed $value): void
    {
        $this->expectException(InvalidTypeException::class);

        DimensionValidator::validate($value);
    }

    public static function invalidTypeProvider(): array
    {
        return [
            'float' => [100.5],
            'string' => ['100'],
            'null' => [null],
            'boolean true' => [true],
            'boolean false' => [false],
            'array' => [[100]],
            'object' => [new \stdClass()],
            'numeric string' => ['500'],
            'empty string' => [''],
        ];
    }

    /**
     * @dataProvider aboveMaximumProvider
     */
    public function testValidateThrowsForOutOfRangeValues(int $dimension): void
    {
        $this->expectException(InvalidRangeException::class);
        $this->expectExceptionMessage('out of range');
        $this->expectExceptionMessage('10');
        $this->expectExceptionMessage('2000');

        DimensionValidator::validate($dimension);
    }

    public static function aboveMaximumProvider(): array
    {
        return [
            'negative small' => [-1],
            'zero' => [0],
            'five' => [5],
            'just below minimum' => [9],
            'just above maximum' => [2001],
            'moderately above' => [2500],
            'far above' => [5000],
            'very large' => [10000],
            'extremely large' => [100000],
        ];
    }
}
