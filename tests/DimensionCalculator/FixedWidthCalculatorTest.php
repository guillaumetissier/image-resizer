<?php

declare(strict_types=1);

namespace DimensionCalculator;

use Guillaumetissier\ImageResizer\DimensionCalculator\FixedWidthCalculator;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Exceptions\MissingKeyException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use PHPUnit\Framework\TestCase;

final class FixedWidthCalculatorTest extends TestCase
{
    public function testCalculateDimensionsKeepsAspectRatio(): void
    {
        $calculator = new FixedWidthCalculator(['setWidth' => 200]);
        $original = new ImageDimensions(400, 800);
        $result = $calculator->calculateDimensions($original);

        $this->assertInstanceOf(ImageDimensions::class, $result);
        $this->assertSame(100, $result->getHeight());
        $this->assertSame(200, $result->getWidth());
    }

    public function testMissingHeightThrowsException(): void
    {
        $this->expectException(MissingKeyException::class);

        new FixedWidthCalculator([]);
    }

    public function testNonIntegerHeightThrowsException(): void
    {
        $this->expectException(InvalidTypeException::class);

        new FixedWidthCalculator(['setWidth' => '200']);
    }
}
