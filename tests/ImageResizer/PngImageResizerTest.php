<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\ImageResizer;

use Guillaumetissier\ImageResizer\Constants\Options;
use Guillaumetissier\ImageResizer\Exceptions\CannotCreateImageException;
use Guillaumetissier\ImageResizer\Exceptions\CannotSaveImageException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use Guillaumetissier\ImageResizer\ImageResizer\PngImageResizer;
use Guillaumetissier\ImageResizer\ImageResizerConfig;
use Guillaumetissier\PathUtilities\Path;
use PHPUnit\Framework\TestCase;

final class PngImageResizerTest extends TestCase
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
        $this->sourceFile = sys_get_temp_dir().'/source.png';
        $this->targetFile = sys_get_temp_dir().'/target.png';

        $image = imagecreatetruecolor(120, 80);

        imagealphablending($image, false);
        imagesavealpha($image, true);

        imagepng($image, $this->sourceFile);
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

    public function testResizePngImage(): void
    {
        $resizer = new PngImageResizer(self::$config, []);
        $newDimensions = new ImageDimensions(40, 60); // height, width
        $resizer->resize(new Path($this->sourceFile), new Path($this->targetFile), $newDimensions);

        $this->assertFileExists($this->targetFile);
        $infos = getimagesize($this->targetFile);
        $this->assertSame(60, $infos[0]); // width
        $this->assertSame(40, $infos[1]); // height
    }

    public function testResizePngWithCustomQuality(): void
    {
        $resizer = new PngImageResizer(self::$config, [Options::QUALITY->value => 80]);
        $resizer->resize(new Path($this->sourceFile), new Path($this->targetFile), new ImageDimensions(40, 60));

        $this->assertFileExists($this->targetFile);
    }

    public function testCannotCreateImageThrowsException(): void
    {
        $this->expectException(CannotCreateImageException::class);

        $resizer = new PngImageResizer(self::$config, []);
        $resizer->resize(
            new Path('/path/to/nonexistent.png'),
            new Path($this->targetFile),
            new ImageDimensions(10, 10)
        );
    }

    public function testCannotSaveImageThrowsException(): void
    {
        $this->expectException(CannotSaveImageException::class);

        $resizer = new PngImageResizer(self::$config, []);
        $resizer->resize(
            new Path($this->sourceFile),
            new Path('/invalid/path/target.png'),
            new ImageDimensions(10, 10)
        );
    }
}
