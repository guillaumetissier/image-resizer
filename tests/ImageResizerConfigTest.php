<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests;

use Guillaumetissier\ImageResizer\ImageResizerConfig;
use PHPUnit\Framework\TestCase;

final class ImageResizerConfigTest extends TestCase
{
    public function testConstructorWithDefaultValues(): void
    {
        $config = new ImageResizerConfig();

        $this->assertSame(100, $config->minWidth);
        $this->assertSame(2000, $config->maxWidth);
        $this->assertSame(100, $config->minHeight);
        $this->assertSame(2000, $config->maxHeight);
        $this->assertSame(0.01, $config->minRatio);
        $this->assertSame(10.0, $config->maxRatio);
        $this->assertSame(0, $config->minQuality);
        $this->assertSame(100, $config->maxQuality);
        $this->assertSame(80, $config->defaultQuality);
        $this->assertFalse($config->defaultInterlace);
    }

    public function testConstructorWithCustomValues(): void
    {
        $config = new ImageResizerConfig([
            'minWidth' => 200,
            'maxWidth' => 1000,
            'minHeight' => 150,
            'maxHeight' => 800,
            'minRatio' => 0.5,
            'maxRatio' => 2.0,
            'minQuality' => 50,
            'maxQuality' => 90,
            'defaultQuality' => 75,
            'defaultInterlace' => true,
        ]);

        $this->assertSame(200, $config->minWidth);
        $this->assertSame(1000, $config->maxWidth);
        $this->assertSame(150, $config->minHeight);
        $this->assertSame(800, $config->maxHeight);
        $this->assertSame(0.5, $config->minRatio);
        $this->assertSame(2.0, $config->maxRatio);
        $this->assertSame(50, $config->minQuality);
        $this->assertSame(90, $config->maxQuality);
        $this->assertSame(75, $config->defaultQuality);
        $this->assertTrue($config->defaultInterlace);
    }

    public function testConstrainsDimensionsToAbsoluteMinimum(): void
    {
        $config = new ImageResizerConfig([
            'minWidth' => -100,
            'maxWidth' => 0,
            'minHeight' => -50,
            'maxHeight' => 0,
        ]);

        $this->assertSame(1, $config->minWidth);
        $this->assertSame(1, $config->maxWidth);
        $this->assertSame(1, $config->minHeight);
        $this->assertSame(1, $config->maxHeight);
    }

    public function testConstrainsDimensionsToAbsoluteMaximum(): void
    {
        $config = new ImageResizerConfig([
            'minWidth' => 60000,
            'maxWidth' => 100000,
            'minHeight' => 70000,
            'maxHeight' => 80000,
        ]);

        $this->assertSame(50000, $config->minWidth);
        $this->assertSame(50000, $config->maxWidth);
        $this->assertSame(50000, $config->minHeight);
        $this->assertSame(50000, $config->maxHeight);
    }

    public function testSwapsMinMaxWhenMinIsGreaterThanMax(): void
    {
        $config = new ImageResizerConfig([
            'minWidth' => 1000,
            'maxWidth' => 500,
            'minHeight' => 800,
            'maxHeight' => 400,
        ]);

        // Should swap min and max
        $this->assertSame(500, $config->minWidth);
        $this->assertSame(1000, $config->maxWidth);
        $this->assertSame(400, $config->minHeight);
        $this->assertSame(800, $config->maxHeight);
    }

    public function testConstrainsRatioToAbsoluteMinimum(): void
    {
        $config = new ImageResizerConfig([
            'minRatio' => -1.0,
            'maxRatio' => 0.001,
        ]);

        $this->assertSame(0.01, $config->minRatio);
        $this->assertSame(0.01, $config->maxRatio);
    }

    public function testConstrainsRatioToAbsoluteMaximum(): void
    {
        $config = new ImageResizerConfig([
            'minRatio' => 15.0,
            'maxRatio' => 20.0,
        ]);

        $this->assertSame(10.0, $config->minRatio);
        $this->assertSame(10.0, $config->maxRatio);
    }

    public function testSwapsMinMaxRatioWhenMinIsGreaterThanMax(): void
    {
        $config = new ImageResizerConfig([
            'minRatio' => 2.0,
            'maxRatio' => 0.5,
        ]);

        // Should swap min and max
        $this->assertSame(0.5, $config->minRatio);
        $this->assertSame(2.0, $config->maxRatio);
    }

    public function testConstrainsQualityToAbsoluteMinimum(): void
    {
        $config = new ImageResizerConfig([
            'minQuality' => -50,
            'maxQuality' => -10,
            'defaultQuality' => -20,
        ]);

        $this->assertSame(0, $config->minQuality);
        $this->assertSame(0, $config->maxQuality);
        $this->assertSame(0, $config->defaultQuality);
    }

    public function testConstrainsQualityToAbsoluteMaximum(): void
    {
        $config = new ImageResizerConfig([
            'minQuality' => 110,
            'maxQuality' => 150,
            'defaultQuality' => 120,
        ]);

        $this->assertSame(100, $config->minQuality);
        $this->assertSame(100, $config->maxQuality);
        $this->assertSame(100, $config->defaultQuality);
    }

    public function testSwapsMinMaxQualityWhenMinIsGreaterThanMax(): void
    {
        $config = new ImageResizerConfig([
            'minQuality' => 90,
            'maxQuality' => 60,
        ]);

        // Should swap min and max
        $this->assertSame(60, $config->minQuality);
        $this->assertSame(90, $config->maxQuality);
    }

    public function testAdjustsDefaultQualityToMinQuality(): void
    {
        $config = new ImageResizerConfig([
            'minQuality' => 70,
            'maxQuality' => 90,
            'defaultQuality' => 50, // Below min
        ]);

        // defaultQuality should be adjusted to minQuality
        $this->assertSame(70, $config->defaultQuality);
    }

    public function testAdjustsDefaultQualityToMaxQuality(): void
    {
        $config = new ImageResizerConfig([
            'minQuality' => 60,
            'maxQuality' => 80,
            'defaultQuality' => 95, // Above max
        ]);

        // defaultQuality should be adjusted to maxQuality
        $this->assertSame(80, $config->defaultQuality);
    }

    public function testDefaultFactory(): void
    {
        $config = ImageResizerConfig::default();

        $this->assertInstanceOf(ImageResizerConfig::class, $config);
        $this->assertSame(100, $config->minWidth);
        $this->assertSame(2000, $config->maxWidth);
        $this->assertSame(80, $config->defaultQuality);
    }

    public function testSafeFactory(): void
    {
        $config = ImageResizerConfig::safe();

        $this->assertInstanceOf(ImageResizerConfig::class, $config);
        $this->assertSame(10, $config->minWidth);
        $this->assertSame(8000, $config->maxWidth);
        $this->assertSame(10, $config->minHeight);
        $this->assertSame(8000, $config->maxHeight);
        $this->assertSame(0.1, $config->minRatio);
        $this->assertSame(2.0, $config->maxRatio);
        $this->assertSame(50, $config->minQuality);
        $this->assertSame(95, $config->maxQuality);
        $this->assertSame(85, $config->defaultQuality);
    }

    public function testStrictFactory(): void
    {
        $config = ImageResizerConfig::strict();

        $this->assertInstanceOf(ImageResizerConfig::class, $config);
        $this->assertSame(100, $config->minWidth);
        $this->assertSame(4000, $config->maxWidth);
        $this->assertSame(100, $config->minHeight);
        $this->assertSame(4000, $config->maxHeight);
        $this->assertSame(0.5, $config->minRatio);
        $this->assertSame(1.0, $config->maxRatio);
        $this->assertSame(60, $config->minQuality);
        $this->assertSame(90, $config->maxQuality);
        $this->assertSame(80, $config->defaultQuality);
    }

    public function testThumbnailFactory(): void
    {
        $config = ImageResizerConfig::thumbnail();

        $this->assertInstanceOf(ImageResizerConfig::class, $config);
        $this->assertSame(500, $config->maxWidth);
        $this->assertSame(500, $config->maxHeight);
        $this->assertSame(70, $config->minQuality);
        $this->assertSame(85, $config->maxQuality);
        $this->assertSame(75, $config->defaultQuality);
    }

    public function testWebFactory(): void
    {
        $config = ImageResizerConfig::web();

        $this->assertInstanceOf(ImageResizerConfig::class, $config);
        $this->assertSame(2000, $config->maxWidth);
        $this->assertSame(2000, $config->maxHeight);
        $this->assertSame(70, $config->minQuality);
        $this->assertSame(90, $config->maxQuality);
        $this->assertSame(80, $config->defaultQuality);
    }

    public function testPrintFactory(): void
    {
        $config = ImageResizerConfig::print();

        $this->assertInstanceOf(ImageResizerConfig::class, $config);
        $this->assertSame(10000, $config->maxWidth);
        $this->assertSame(10000, $config->maxHeight);
        $this->assertSame(85, $config->minQuality);
        $this->assertSame(100, $config->maxQuality);
        $this->assertSame(95, $config->defaultQuality);
    }

    public function testPartialConfiguration(): void
    {
        $config = new ImageResizerConfig([
            'maxWidth' => 1500,
            'defaultQuality' => 85,
        ]);

        // Should have custom values
        $this->assertSame(1500, $config->maxWidth);
        $this->assertSame(85, $config->defaultQuality);

        // Should have default values for others
        $this->assertSame(100, $config->minWidth);
        $this->assertSame(100, $config->minHeight);
        $this->assertSame(2000, $config->maxHeight);
    }

    public function testEdgeCaseWithAllNullValues(): void
    {
        $config = new ImageResizerConfig([
            'minWidth' => null,
            'maxWidth' => null,
            'minHeight' => null,
            'maxHeight' => null,
            'minRatio' => null,
            'maxRatio' => null,
            'minQuality' => null,
            'maxQuality' => null,
        ]);

        // Should still have defaults applied
        $this->assertSame(100, $config->minWidth);
        $this->assertSame(2000, $config->maxWidth);
        $this->assertSame(80, $config->defaultQuality);
    }

    public function testConfigurationConsistency(): void
    {
        $config = new ImageResizerConfig([
            'minWidth' => 100,
            'maxWidth' => 1000,
            'minHeight' => 100,
            'maxHeight' => 1000,
            'minQuality' => 50,
            'maxQuality' => 90,
            'defaultQuality' => 70,
        ]);

        // Verify min <= max for all constrained values
        $this->assertLessThanOrEqual($config->maxWidth, $config->minWidth);
        $this->assertLessThanOrEqual($config->maxHeight, $config->minHeight);
        $this->assertLessThanOrEqual($config->maxRatio, $config->minRatio);
        $this->assertLessThanOrEqual($config->maxQuality, $config->minQuality);

        // Verify defaultQuality is within bounds
        $this->assertGreaterThanOrEqual($config->minQuality, $config->defaultQuality);
        $this->assertLessThanOrEqual($config->maxQuality, $config->defaultQuality);
    }
}
