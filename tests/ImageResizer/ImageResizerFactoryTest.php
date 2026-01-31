<?php

namespace Guillaumetissier\ImageResizer\Tests\ImageResizer;

use Guillaumetissier\ImageResizer\Exceptions\InvalidImageTypeException;
use Guillaumetissier\ImageResizer\ImageResizer\GifImageResizer;
use Guillaumetissier\ImageResizer\ImageResizer\ImageResizerFactory;
use Guillaumetissier\ImageResizer\ImageResizer\JpegImageResizer;
use Guillaumetissier\ImageResizer\ImageResizer\PngImageResizer;
use Guillaumetissier\ImageResizer\ImageResizerConfig;
use Guillaumetissier\PathUtilities\Path;
use PHPUnit\Framework\TestCase;

class ImageResizerFactoryTest extends TestCase
{
    private ImageResizerFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ImageResizerFactory();
    }

    /**
     * @dataProvider createProvider
     */
    public function testCreate(Path $path, ImageResizerConfig $config, string $expectedResizer): void
    {
        $this->assertInstanceOf($expectedResizer, $this->factory->create($path, $config, []));
    }

    public static function createProvider(): \Generator
    {
        yield [new Path('/my/file.jpeg'), ImageResizerConfig::safe(), JpegImageResizer::class];
        yield [new Path('/my/other/file.jpg'), ImageResizerConfig::strict(), JpegImageResizer::class];
        yield [new Path('/an/other/file.png'), ImageResizerConfig::thumbnail(), PngImageResizer::class];
        yield [new Path('/some/other/file.gif'), ImageResizerConfig::web(), GifImageResizer::class];
    }

    /**
     * @dataProvider createWithExceptionProvider
     */
    public function testCreateWithException(Path $path, ImageResizerConfig $config): void
    {
        $this->expectException(InvalidImageTypeException::class);

        $this->factory->create($path, $config, []);
    }

    public static function createWithExceptionProvider(): \Generator
    {
        yield [new Path('/a/file/without/extension'), ImageResizerConfig::print()];
        yield [new Path('/a/file/with/wrong/extension.dummy'), ImageResizerConfig::default()];
    }
}
