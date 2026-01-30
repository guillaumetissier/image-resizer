<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\DimensionReader;

use Guillaumetissier\ImageResizer\DimensionReader\DimensionsReader;
use Guillaumetissier\ImageResizer\Exceptions\CannotReadImageSizeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidImageTypeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidPathException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use Guillaumetissier\PathUtilities\Path;
use PHPUnit\Framework\TestCase;

final class DimensionsReaderTest extends TestCase
{
    private string $testDir;

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir().'/image-resizer-test-'.uniqid();
        mkdir($this->testDir, 0755, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testDir)) {
            $files = glob($this->testDir.'/*') ?: [];
            array_map('unlink', $files);
            rmdir($this->testDir);
        }
    }

    /**
     * @dataProvider supportedFormatProvider
     */
    public function testReadDimensionsForSupportedFormats(string $format, string $imageData): void
    {
        $file = $this->testDir."/test.{$format}";
        file_put_contents($file, base64_decode($imageData));

        $reader = new DimensionsReader();
        $dimensions = $reader->readDimensions(new Path($file));

        $this->assertInstanceOf(ImageDimensions::class, $dimensions);
        $this->assertGreaterThan(0, $dimensions->getWidth());
        $this->assertGreaterThan(0, $dimensions->getHeight());
    }

    public static function supportedFormatProvider(): array
    {
        return [
            'png' => [
                'png',
                // Minimal 1x1 PNG
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==',
            ],
            'gif' => [
                'gif',
                // Minimal 1x1 GIF
                'R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==',
            ],
            'jpg' => [
                'jpg',
                // Minimal 1x1 JPEG
                '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCwAA8A/9k=',
            ],
            'jpeg' => [
                'jpeg',
                // Minimal 1x1 JPEG
                '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCwAA8A/9k=',
            ],
        ];
    }

    public function testThrowsExceptionWhenFileDoesNotExist(): void
    {
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessage('not a file');

        $reader = new DimensionsReader();
        $reader->readDimensions(new Path($this->testDir.'/does-not-exist.jpg'));
    }

    public function testThrowsExceptionForCorruptedImage(): void
    {
        $file = $this->testDir.'/corrupted.jpg';
        file_put_contents($file, 'not a valid image');

        $this->expectException(CannotReadImageSizeException::class);

        $reader = new DimensionsReader();
        $reader->readDimensions(new Path($file));
    }

    /**
     * @dataProvider unsupportedFormatProvider
     */
    public function testThrowsExceptionForUnsupportedFormats(string $format, string $imageData): void
    {
        $file = $this->testDir."/test.{$format}";
        file_put_contents($file, base64_decode($imageData));

        $this->expectException(InvalidImageTypeException::class);
        $this->expectExceptionMessage('Unsupported type');

        $reader = new DimensionsReader();
        $reader->readDimensions(new Path($file));
    }

    public static function unsupportedFormatProvider(): array
    {
        return [
            'bmp' => [
                'bmp',
                // Minimal 1x1 BMP (24-bit)
                'Qk0+AAAAAAAAADYAAAAoAAAAAQAAAAEAAAABABgAAAAAAAAAAADEDgAAxA4AAAAAAAAAAAAA/wAA',
            ],
            'webp' => [
                'webp',
                // Minimal 1x1 WebP
                'UklGRiQAAABXRUJQVlA4IBgAAAAwAQCdASoBAAEAAwA0JaQAA3AA/vuUAAA=',
            ],
        ];
    }

    public function testReadDimensionsReturnsCorrectWidthAndHeight(): void
    {
        // Create a 10x20 PNG image
        $image = imagecreatetruecolor(10, 20);
        $file = $this->testDir.'/test-dimensions.png';
        imagepng($image, $file);
        imagedestroy($image);

        $reader = new DimensionsReader();
        $dimensions = $reader->readDimensions(new Path($file));

        $this->assertSame(10, $dimensions->getWidth());
        $this->assertSame(20, $dimensions->getHeight());
    }

    public function testReadDimensionsWorksWithDifferentSizes(): void
    {
        $sizes = [
            ['width' => 100, 'height' => 50],
            ['width' => 800, 'height' => 600],
            ['width' => 1920, 'height' => 1080],
        ];

        foreach ($sizes as $size) {
            $image = imagecreatetruecolor($size['width'], $size['height']);
            $file = $this->testDir."/test-{$size['width']}x{$size['height']}.png";
            imagepng($image, $file);
            imagedestroy($image);

            $reader = new DimensionsReader();
            $dimensions = $reader->readDimensions(new Path($file));

            $this->assertSame($size['width'], $dimensions->getWidth());
            $this->assertSame($size['height'], $dimensions->getHeight());
        }
    }
}
