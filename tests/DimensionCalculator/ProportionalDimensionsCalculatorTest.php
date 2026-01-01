<?php

declare(strict_types=1);

namespace DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\DimensionCalculator\ProportionalDimensionsCalculator;
use Guillaumetissier\ImageResizer\Exceptions\MissingTransformationException;
use Guillaumetissier\ImageResizer\Exceptions\WrongValueTypeException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use PHPUnit\Framework\TestCase;

final class ProportionalDimensionsCalculatorTest extends TestCase
{
    public function testCalculateDimensionsAppliesRatio(): void
    {
        $calculator = new ProportionalDimensionsCalculator([Transformations::SET_RATIO->value => 0.5]);
        $original = new ImageDimensions(400, 800); // height, width
        $result = $calculator->calculateDimensions($original);

        $this->assertInstanceOf(ImageDimensions::class, $result);
        $this->assertSame(200, $result->getHeight());
        $this->assertSame(400, $result->getWidth());
    }

    public function testRatioAsNumericStringIsAccepted(): void
    {
        $calculator = new ProportionalDimensionsCalculator([Transformations::SET_RATIO->value => '2']);
        $original = new ImageDimensions(100, 150);
        $result = $calculator->calculateDimensions($original);

        $this->assertSame(200, $result->getHeight());
        $this->assertSame(300, $result->getWidth());
    }

    public function testMissingRatioThrowsException(): void
    {
        $this->expectException(MissingTransformationException::class);

        new ProportionalDimensionsCalculator([]);
    }

    public function testNonNumericRatioThrowsException(): void
    {
        $this->expectException(WrongValueTypeException::class);

        new ProportionalDimensionsCalculator([Transformations::SET_RATIO->value => 'abc']);
    }
}
