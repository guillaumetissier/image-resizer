<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\DimensionCalculator\FixedHeightCalculator;
use Guillaumetissier\ImageResizer\Exceptions\MissingTransformationException;
use Guillaumetissier\ImageResizer\Exceptions\WrongValueTypeException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use PHPUnit\Framework\TestCase;

final class FixedHeightCalculatorTest extends TestCase
{
    public function testCalculateDimensionsKeepsAspectRatio(): void
    {
        $calculator = new FixedHeightCalculator([Transformations::SET_HEIGHT->value => 200]);
        $original = new ImageDimensions(400, 800); // height, width
        $result = $calculator->calculateDimensions($original);

        $this->assertInstanceOf(ImageDimensions::class, $result);
        $this->assertSame(200, $result->getHeight());
        $this->assertSame(400, $result->getWidth());
    }

    public function testMissingHeightThrowsException(): void
    {
        $this->expectException(MissingTransformationException::class);

        new FixedHeightCalculator([]);
    }

    public function testNonIntegerHeightThrowsException(): void
    {
        $this->expectException(WrongValueTypeException::class);

        new FixedHeightCalculator([Transformations::SET_HEIGHT->value => '200']);
    }
}
