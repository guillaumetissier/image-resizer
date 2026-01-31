<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\ImageResizer;

use Guillaumetissier\ImageResizer\Exceptions\CannotCreateImageException;
use Guillaumetissier\ImageResizer\Exceptions\CannotSaveImageException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use Guillaumetissier\ImageResizer\ImageResizer\GifImageResizer;
use Guillaumetissier\ImageResizer\ImageResizerConfig;
use Guillaumetissier\PathUtilities\Path;
use PHPUnit\Framework\TestCase;

final class GifImageResizerTest extends TestCase
{
    private static ImageResizerConfig $config;

    private string $sourceFile;

    private string $targetFile;

    public static function setUpBeforeClass(): void
    {
        self::$config = new ImageResizerConfig();
    }

    protected function setUp(): void
    {
        $this->sourceFile = sys_get_temp_dir().'/source.gif';
        $this->targetFile = sys_get_temp_dir().'/target.gif';

        $image = imagecreatetruecolor(100, 50);
        imagegif($image, $this->sourceFile);
        imagedestroy($image);
    }

    protected function tearDown(): void
    {
        if (is_file($this->sourceFile)) {
            unlink($this->sourceFile);
        }

        if (is_file($this->targetFile)) {
            unlink($this->targetFile);
        }
    }

    public function testResizeGifImage(): void
    {
        $resizer = new GifImageResizer(self::$config, []);
        $newDimensions = new ImageDimensions(25, 50); // height, width
        $resizer->resize(new Path($this->sourceFile), new Path($this->targetFile), $newDimensions);

        $this->assertFileExists($this->targetFile);

        $infos = getimagesize($this->targetFile);

        $this->assertSame(50, $infos[0]); // width
        $this->assertSame(25, $infos[1]); // height
    }

    public function testCannotCreateImageThrowsException(): void
    {
        $this->expectException(CannotCreateImageException::class);

        $resizer = new GifImageResizer(self::$config, []);
        $resizer->resize(new Path('/path/to/nonexistent.gif'), new Path($this->targetFile), new ImageDimensions(10, 10));
    }

    public function testCannotSaveImageThrowsException(): void
    {
        $this->expectException(CannotSaveImageException::class);

        $resizer = new GifImageResizer(self::$config, []);
        $invalidTarget = '/invalid/path/target.gif';
        $resizer->resize(new Path($this->sourceFile), new Path($invalidTarget), new ImageDimensions(10, 10));
    }
}
