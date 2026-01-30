<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\DimensionReader;

use Guillaumetissier\ImageResizer\DimensionReader\DimensionsReader;
use Guillaumetissier\ImageResizer\Exceptions\CannotReadImageSizeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidPathException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use Guillaumetissier\PathUtilities\Path;
use PHPUnit\Framework\TestCase;

final class DimensionsReaderTest extends TestCase
{
    private string $imageFile;

    protected function setUp(): void
    {
        $this->imageFile = sys_get_temp_dir().'/test_image_dimensions.png';
        $image = imagecreatetruecolor(300, 200); // width, height
        imagepng($image, $this->imageFile);
        imagedestroy($image);
    }

    protected function tearDown(): void
    {
        if (is_file($this->imageFile)) {
            unlink($this->imageFile);
        }
    }

    public function testReadDimensionsReturnsImageDimensions(): void
    {
        $reader = new DimensionsReader();
        $dimensions = $reader->readDimensions(new Path($this->imageFile));

        $this->assertInstanceOf(ImageDimensions::class, $dimensions);
        $this->assertSame(200, $dimensions->getHeight());
        $this->assertSame(300, $dimensions->getWidth());
    }

    public function testFileNotFoundThrowsException(): void
    {
        $this->expectException(InvalidPathException::class);

        $reader = new DimensionsReader();
        $reader->readDimensions(new Path('/path/to/nonexistent/file.png'));
    }

    public function testUnreadableImageThrowsException(): void
    {
        $this->expectException(CannotReadImageSizeException::class);

        $file = sys_get_temp_dir().'/not_an_image.txt';
        file_put_contents($file, 'not an image');

        try {
            $reader = new DimensionsReader();
            $reader->readDimensions(new Path($file));
        } finally {
            unlink($file);
        }
    }
}
