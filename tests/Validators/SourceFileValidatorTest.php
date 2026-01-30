<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\Validators;

use Guillaumetissier\ImageResizer\Exceptions\InvalidPathException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Validators\SourceFileValidator;
use Guillaumetissier\PathUtilities\Path;
use PHPUnit\Framework\TestCase;

final class SourceFileValidatorTest extends TestCase
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
            array_map('unlink', glob($this->testDir.'/*'));
            rmdir($this->testDir);
        }
    }

    /**
     * @dataProvider validFileProvider
     */
    public function testValidateAcceptsValidFiles(string $extension): void
    {
        $file = $this->testDir.'/test.'.$extension;
        file_put_contents($file, 'test content');
        chmod($file, 0644);

        SourceFileValidator::validate(new Path($file));

        $this->assertTrue(true);
    }

    public static function validFileProvider(): array
    {
        return [
            'jpg' => ['jpg'],
            'jpeg' => ['jpeg'],
            'png' => ['png'],
            'gif' => ['gif'],
        ];
    }

    /**
     * @dataProvider invalidTypeProvider
     */
    public function testValidateThrowsForInvalidTypes(mixed $value): void
    {
        $this->expectException(InvalidTypeException::class);

        SourceFileValidator::validate($value);
    }

    public static function invalidTypeProvider(): array
    {
        return [
            'string' => ['/path/to/file.jpg'],
            'int' => [123],
            'float' => [1.5],
            'null' => [null],
            'boolean' => [true],
            'array' => [['/path/to/file.jpg']],
            'object' => [new \stdClass()],
        ];
    }

    public function testValidateThrowsWhenFileDoesNotExist(): void
    {
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessage('not found');

        $nonExistent = new Path($this->testDir.'/does-not-exist.jpg');
        SourceFileValidator::validate($nonExistent);
    }

    public function testValidateThrowsWhenPathIsDirectory(): void
    {
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessage('not a file');

        SourceFileValidator::validate(new Path($this->testDir));
    }

    public function testValidateThrowsWhenFileIsNotReadable(): void
    {
        $file = $this->testDir.'/unreadable.jpg';
        file_put_contents($file, 'test');
        chmod($file, 0000);

        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessage('not readable');

        try {
            SourceFileValidator::validate(new Path($file));
        } finally {
            chmod($file, 0644); // Restore permissions for cleanup
        }
    }

    /**
     * @dataProvider unsupportedExtensionProvider
     */
    public function testValidateThrowsForUnsupportedFormats(string $extension): void
    {
        $file = $this->testDir.'/test.'.$extension;
        file_put_contents($file, 'test content');

        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessage('invalid format');

        SourceFileValidator::validate(new Path($file));
    }

    public static function unsupportedExtensionProvider(): array
    {
        return [
            'txt' => ['txt'],
            'pdf' => ['pdf'],
            'doc' => ['doc'],
            'mp4' => ['mp4'],
            'bmp' => ['bmp'],
            'svg' => ['svg'],
            'tiff' => ['tiff'],
            'webp' => ['webp'],
        ];
    }
}
