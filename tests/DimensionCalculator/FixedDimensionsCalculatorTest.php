<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\DimensionCalculator;

use Guillaumetissier\ImageResizer\DimensionCalculator\FixedDimensionsCalculator;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Exceptions\MissingKeyException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use PHPUnit\Framework\TestCase;

final class FixedDimensionsCalculatorTest extends TestCase
{
    public function testCalculateDimensionsReturnsFixedDimensions(): void
    {
        $calculator = new FixedDimensionsCalculator(['setHeight' => 200, 'setWidth' => 300]);
        $original = new ImageDimensions(800, 600);
        $result = $calculator->calculateDimensions($original);

        $this->assertInstanceOf(ImageDimensions::class, $result);
        $this->assertSame(200, $result->getHeight());
        $this->assertSame(300, $result->getWidth());
    }

    public function testMissingHeightThrowsException(): void
    {
        $this->expectException(MissingKeyException::class);

        new FixedDimensionsCalculator(['setWidth' => 300]);
    }

    public function testMissingWidthThrowsException(): void
    {
        $this->expectException(MissingKeyException::class);

        new FixedDimensionsCalculator(['setHeight' => 200]);
    }

    public function testNonIntegerHeightThrowsException(): void
    {
        $this->expectException(InvalidTypeException::class);

        new FixedDimensionsCalculator(['setHeight' => '200', 'setWidth' => 300]);
    }

    public function testNonIntegerWidthThrowsException(): void
    {
        $this->expectException(InvalidTypeException::class);

        new FixedDimensionsCalculator(['setHeight' => 200, 'setWidth' => '300']);
    }
}
