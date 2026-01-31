<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\Validators;

use Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Validators\ScaleModeValidator;
use PHPUnit\Framework\TestCase;

final class ScaleModeValidatorTest extends TestCase
{
    private ScaleModeValidator $validator;

    public function setUp(): void
    {
        $this->validator = new ScaleModeValidator();
    }

    /**
     * @dataProvider validScaleModeProvider
     */
    public function testValidateAcceptsValidScaleMode(int $scaleMode): void
    {
        $this->validator->validate($scaleMode);

        $this->assertTrue(true);
    }

    public static function validScaleModeProvider(): array
    {
        return [
            'IMG_NEAREST_NEIGHBOUR' => [IMG_NEAREST_NEIGHBOUR],
            'IMG_BILINEAR_FIXED' => [IMG_BILINEAR_FIXED],
            'IMG_BICUBIC' => [IMG_BICUBIC],
            'IMG_BICUBIC_FIXED' => [IMG_BICUBIC_FIXED],
        ];
    }

    /**
     * @dataProvider invalidScaleModeProvider
     */
    public function testValidateAcceptsInvalidScaleMode(int $invalidScaleMode): void
    {
        $this->expectException(InvalidRangeException::class);

        $this->validator->validate($invalidScaleMode);
    }

    public static function invalidScaleModeProvider(): array
    {
        return [
            'integer 1' => [1],
            'integer 2' => [2],
            'integer 6' => [6],
            'integer 15' => [15],
        ];
    }

    /**
     * @dataProvider invalidTypeProvider
     */
    public function testValidateThrowsForInvalidTypes(mixed $value): void
    {
        $this->expectException(InvalidTypeException::class);

        $this->validator->validate($value);
    }

    public static function invalidTypeProvider(): array
    {
        return [
            'float' => [100.5],
            'string' => ['100'],
            'null' => [null],
            'array' => [[100]],
            'object' => [new \stdClass()],
            'numeric string' => ['500'],
            'empty string' => [''],
        ];
    }
}
