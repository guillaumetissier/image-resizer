<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\ImageResizer;

use Guillaumetissier\ImageResizer\Exceptions\UnsupportedImageTypeException;
use Guillaumetissier\ImageResizer\ImageResizer\GifImageResizer;
use Guillaumetissier\ImageResizer\ImageResizer\ImageResizerFactory;
use Guillaumetissier\ImageResizer\ImageResizer\JpegImageResizer;
use Guillaumetissier\ImageResizer\ImageResizer\PngImageResizer;
use Guillaumetissier\PathUtilities\Path;
use PHPUnit\Framework\TestCase;

final class ImageResizerFactoryTest extends TestCase
{
    /**
     * @dataProvider dataCreate
     */
    public function testCreate(string $path, string $expectedInstance): void
    {
        $factory = new ImageResizerFactory();
        $resizer = $factory->create(new Path($path), []);

        $this->assertInstanceOf($expectedInstance, $resizer);
    }

    public static function dataCreate(): \Generator
    {
        yield ['image.gif', GifImageResizer::class];
        yield ['image.jpg', JpegImageResizer::class];
        yield ['image.jpeg', JpegImageResizer::class];
        yield ['image.png', PngImageResizer::class];
    }

    public function testUnsupportedExtensionThrowsException(): void
    {
        $this->expectException(UnsupportedImageTypeException::class);

        $factory = new ImageResizerFactory();
        $factory->create(new Path('image.bmp'), []);
    }
}
