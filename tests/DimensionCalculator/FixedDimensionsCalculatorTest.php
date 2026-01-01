<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\DimensionCalculator\FixedDimensionsCalculator;
use Guillaumetissier\ImageResizer\Exceptions\MissingTransformationException;
use Guillaumetissier\ImageResizer\Exceptions\WrongValueTypeException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use PHPUnit\Framework\TestCase;

final class FixedDimensionsCalculatorTest extends TestCase
{
    public function testCalculateDimensionsReturnsFixedDimensions(): void
    {
        $calculator = new FixedDimensionsCalculator([
            Transformations::SET_HEIGHT->value => 200,
            Transformations::SET_WIDTH->value => 300,
        ]);
        $original = new ImageDimensions(800, 600);
        $result = $calculator->calculateDimensions($original);

        $this->assertInstanceOf(ImageDimensions::class, $result);
        $this->assertSame(200, $result->getHeight());
        $this->assertSame(300, $result->getWidth());
    }

    public function testMissingHeightThrowsException(): void
    {
        $this->expectException(MissingTransformationException::class);

        new FixedDimensionsCalculator([
            Transformations::SET_WIDTH->value => 300,
        ]);
    }

    public function testMissingWidthThrowsException(): void
    {
        $this->expectException(MissingTransformationException::class);

        new FixedDimensionsCalculator([
            Transformations::SET_HEIGHT->value => 200,
        ]);
    }

    public function testNonIntegerHeightThrowsException(): void
    {
        $this->expectException(WrongValueTypeException::class);

        new FixedDimensionsCalculator([
            Transformations::SET_HEIGHT->value => '200',
            Transformations::SET_WIDTH->value => 300,
        ]);
    }

    public function testNonIntegerWidthThrowsException(): void
    {
        $this->expectException(WrongValueTypeException::class);

        new FixedDimensionsCalculator([
            Transformations::SET_HEIGHT->value => 200,
            Transformations::SET_WIDTH->value => '300',
        ]);
    }
}
