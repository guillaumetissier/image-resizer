<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\Validators;

use Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Validators\QualityValidator;
use PHPUnit\Framework\TestCase;

final class QualityValidatorTest extends TestCase
{
    /**
     * @dataProvider validQualityProvider
     */
    public function testValidateAcceptsValidQualities(int $quality): void
    {
        QualityValidator::validate($quality);

        $this->assertTrue(true);
    }

    public static function validQualityProvider(): array
    {
        return [
            'minimum boundary' => [0],
            'just above minimum' => [1],
            'low quality' => [28],
            'medium quality' => [47],
            'high quality' => [85],
            'just below maximum' => [99],
            'maximum boundary' => [100],
        ];
    }

    /**
     * @dataProvider invalidTypeProvider
     */
    public function testValidateThrowsForInvalidTypes(mixed $value): void
    {
        $this->expectException(InvalidTypeException::class);

        QualityValidator::validate($value);
    }

    public static function invalidTypeProvider(): array
    {
        return [
            'float' => [85.5],
            'string' => ['85'],
            'null' => [null],
            'boolean true' => [true],
            'boolean false' => [false],
            'array' => [[85]],
            'object' => [new \stdClass()],
            'numeric string' => ['50'],
            'empty string' => [''],
        ];
    }

    /**
     * @dataProvider outOfRangeProvider
     */
    public function testValidateThrowsForOutOfRangeValues(int $quality): void
    {
        $this->expectException(InvalidRangeException::class);
        $this->expectExceptionMessage('out of range');
        $this->expectExceptionMessage('0');
        $this->expectExceptionMessage('100');

        QualityValidator::validate($quality);
    }

    public static function outOfRangeProvider(): array
    {
        return [
            'negative' => [-100],
            'just below minimum' => [-1],
            'just above maximum' => [101],
            'above' => [150],
        ];
    }
}
