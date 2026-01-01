<?php

declare(strict_types=1);

namespace DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\DimensionCalculator\FixedWidthCalculator;
use Guillaumetissier\ImageResizer\Exceptions\MissingTransformationException;
use Guillaumetissier\ImageResizer\Exceptions\WrongValueTypeException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use PHPUnit\Framework\TestCase;

final class FixedWidthCalculatorTest extends TestCase
{
    public function testCalculateDimensionsKeepsAspectRatio(): void
    {
        $calculator = new FixedWidthCalculator([Transformations::SET_WIDTH->value => 200]);
        $original = new ImageDimensions(400, 800);
        $result = $calculator->calculateDimensions($original);

        $this->assertInstanceOf(ImageDimensions::class, $result);
        $this->assertSame(100, $result->getHeight());
        $this->assertSame(200, $result->getWidth());
    }

    public function testMissingHeightThrowsException(): void
    {
        $this->expectException(MissingTransformationException::class);

        new FixedWidthCalculator([]);
    }

    public function testNonIntegerHeightThrowsException(): void
    {
        $this->expectException(WrongValueTypeException::class);

        new FixedWidthCalculator([Transformations::SET_WIDTH->value => '200']);
    }
}
