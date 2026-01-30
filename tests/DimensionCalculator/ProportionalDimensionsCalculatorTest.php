<?php

declare(strict_types=1);

namespace DimensionCalculator;

use Guillaumetissier\ImageResizer\DimensionCalculator\ProportionalDimensionsCalculator;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Exceptions\MissingKeyException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use PHPUnit\Framework\TestCase;

final class ProportionalDimensionsCalculatorTest extends TestCase
{
    public function testCalculateDimensionsAppliesRatio(): void
    {
        $calculator = new ProportionalDimensionsCalculator(['setRatio' => 0.5]);
        $original = new ImageDimensions(400, 800); // height, width
        $result = $calculator->calculateDimensions($original);

        $this->assertInstanceOf(ImageDimensions::class, $result);
        $this->assertSame(200, $result->getHeight());
        $this->assertSame(400, $result->getWidth());
    }

    public function testMissingRatioThrowsException(): void
    {
        $this->expectException(MissingKeyException::class);

        new ProportionalDimensionsCalculator([]);
    }

    public function testNonNumericRatioThrowsException(): void
    {
        $this->expectException(InvalidTypeException::class);

        new ProportionalDimensionsCalculator(['setRatio' => 'abc']);
    }

    public function testRatioAsNumericStringThrowsException(): void
    {
        $this->expectException(InvalidTypeException::class);

        new ProportionalDimensionsCalculator(['setRatio' => '2']);
    }
}
