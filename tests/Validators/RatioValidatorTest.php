<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\Validators;

use Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\ImageResizerConfig;
use Guillaumetissier\ImageResizer\Validators\RatioValidator;
use PHPUnit\Framework\TestCase;

final class RatioValidatorTest extends TestCase
{
    private RatioValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new RatioValidator(new ImageResizerConfig(['minRatio' => 0.01, 'maxRatio' => 2.0]));
    }

    /**
     * @dataProvider validRatioProvider
     */
    public function testValidateAcceptsValidRatios(int|float $ratio): void
    {
        $this->validator->validate($ratio);

        $this->assertTrue(true);
    }

    public static function validRatioProvider(): array
    {
        return [
            'minimum boundary' => [0.01],
            'just above minimum' => [0.02],
            'small ratio (50%)' => [0.5],
            'no change (100%)' => [1.0],
            'enlarge (150%)' => [1.5],
            'just below maximum' => [1.99],
            'maximum boundary' => [2.0],
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
            'string' => ['0.5'],
            'null' => [null],
            'boolean true' => [true],
            'boolean false' => [false],
            'array' => [[0.5]],
            'object' => [new \stdClass()],
            'numeric string' => ['1.5'],
            'empty string' => [''],
        ];
    }

    /**
     * @dataProvider outOfRangeProvider
     */
    public function testValidateThrowsForOutOfRangeValues(int|float $ratio): void
    {
        $this->expectException(InvalidRangeException::class);
        $this->expectExceptionMessage('out of range');
        $this->expectExceptionMessage('0.01');
        $this->expectExceptionMessage('2');

        $this->validator->validate($ratio);
    }

    public static function outOfRangeProvider(): array
    {
        return [
            'negative' => [-1.0],
            'zero' => [0],
            'very small' => [0.001],
            'just below minimum' => [0.009],
            'just above maximum' => [2.01],
            'moderately above' => [3.0],
            'far above' => [5.0],
            'very large' => [10.0],
        ];
    }
}
