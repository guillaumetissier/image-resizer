<?php

namespace Guillaumetissier\ImageResizer\Tests\DimensionCalculator;

use Guillaumetissier\ImageResizer\Constants\ResizeType;
use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\DimensionCalculator\DimensionCalculatorFactory;
use Guillaumetissier\ImageResizer\DimensionCalculator\FixedDimensionsCalculator;
use Guillaumetissier\ImageResizer\DimensionCalculator\FixedHeightCalculator;
use Guillaumetissier\ImageResizer\DimensionCalculator\FixedWidthCalculator;
use Guillaumetissier\ImageResizer\DimensionCalculator\ProportionalDimensionsCalculator;
use PHPUnit\Framework\TestCase;

class DimensionCalculatorFactoryTest extends TestCase
{
    private DimensionCalculatorFactory $factory;

    public function setUp(): void
    {
        $this->factory = new DimensionCalculatorFactory();
    }

    /**
     * @dataProvider dataCreate
     */
    public function testCreate(ResizeType $resizeType, array $transformations, string $expectedInstance)
    {
        $this->assertInstanceOf($expectedInstance, $this->factory->create($resizeType, $transformations));
    }

    public static function dataCreate(): \Generator
    {
        yield [
            ResizeType::FIXED,
            [
                Transformations::SET_HEIGHT->value => 100,
                Transformations::SET_WIDTH->value => 100,
            ],
            FixedDimensionsCalculator::class,
        ];

        yield [
            ResizeType::FIXED_HEIGHT,
            [
                Transformations::SET_HEIGHT->value => 100,
            ],
            FixedHeightCalculator::class,
        ];

        yield [
            ResizeType::FIXED_WIDTH,
            [
                Transformations::SET_WIDTH->value => 100,
            ],
            FixedWidthCalculator::class,
        ];

        yield [
            ResizeType::PROPORTIONAL,
            [
                Transformations::SET_RATIO->value => 2,
            ],
            ProportionalDimensionsCalculator::class,
        ];
    }
}
