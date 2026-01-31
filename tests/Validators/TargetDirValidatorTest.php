<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests\Validators;

use Guillaumetissier\ImageResizer\Exceptions\InvalidPathException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Validators\TargetDirValidator;
use Guillaumetissier\PathUtilities\Path;
use PHPUnit\Framework\TestCase;

final class TargetDirValidatorTest extends TestCase
{
    private TargetDirValidator $validator;

    private string $testDir;

    protected function setUp(): void
    {
        $this->validator = new TargetDirValidator();
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

    public function testValidateAcceptsValidDirectory(): void
    {
        $this->validator->validate(new Path($this->testDir));

        $this->assertTrue(true);
    }

    /**
     * @dataProvider invalidTypeProvider
     */
    public function testValidateThrowsForInvalidTypes(mixed $value): void
    {
        $this->expectException(InvalidTypeException::class);

        $this->validator->validate($value);
    }

    public static function invalidTypeProvider(): array
    {
        return [
            'string' => ['/path/to/dir'],
            'int' => [123],
            'float' => [1.5],
            'null' => [null],
            'boolean' => [true],
            'array' => [['/path/to/dir']],
            'object' => [new \stdClass()],
        ];
    }

    public function testValidateThrowsWhenDirectoryDoesNotExist(): void
    {
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessage('not found');

        $nonExistent = new Path($this->testDir.'/does-not-exist');
        $this->validator->validate($nonExistent);
    }

    public function testValidateThrowsWhenPathIsFile(): void
    {
        $file = $this->testDir.'/file.txt';
        file_put_contents($file, 'test');

        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessage('not a directory');

        $this->validator->validate(new Path($file));
    }

    public function testValidateThrowsWhenDirectoryIsNotWritable(): void
    {
        $dir = $this->testDir.'/readonly';
        mkdir($dir, 0555);
        $exceptionThrown = false;

        try {
            $this->validator->validate(new Path($dir));
        } catch (\Throwable $exception) {
            $exceptionThrown = true;
            $this->assertInstanceOf(InvalidPathException::class, $exception);
        } finally {
            rmdir($dir);
        }

        $this->assertTrue($exceptionThrown);
    }
}
