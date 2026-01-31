<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\Validators;

use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Validators\InterlaceValidator;
use PHPUnit\Framework\TestCase;

final class InterlaceValidatorTest extends TestCase
{
    private InterlaceValidator $validator;

    public function setUp(): void
    {
        $this->validator = new InterlaceValidator();
    }

    /**
     * @dataProvider validInterlaceProvider
     */
    public function testValidateAcceptsValidInterlaces(bool $interlace): void
    {
        $this->validator->validate($interlace);

        $this->assertTrue(true);
    }

    public static function validInterlaceProvider(): array
    {
        return [
            'true' => [true],
            'false' => [false],
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
