<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\DimensionCalculator;

use Guillaumetissier\ImageResizer\DimensionCalculator\FixedHeightCalculator;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Exceptions\MissingKeyException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use PHPUnit\Framework\TestCase;

final class FixedHeightCalculatorTest extends TestCase
{
    public function testCalculateDimensionsKeepsAspectRatio(): void
    {
        $calculator = new FixedHeightCalculator(['setHeight' => 200]);
        $original = new ImageDimensions(400, 800); // height, width
        $result = $calculator->calculateDimensions($original);

        $this->assertInstanceOf(ImageDimensions::class, $result);
        $this->assertSame(200, $result->getHeight());
        $this->assertSame(400, $result->getWidth());
    }

    public function testMissingHeightThrowsException(): void
    {
        $this->expectException(MissingKeyException::class);

        new FixedHeightCalculator([]);
    }

    public function testNonIntegerHeightThrowsException(): void
    {
        $this->expectException(InvalidTypeException::class);

        new FixedHeightCalculator(['setHeight' => '200']);
    }
}
