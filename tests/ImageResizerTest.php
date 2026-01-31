<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Tests;

use Guillaumetissier\ImageResizer\Constants\Options;
use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\DimensionCalculator\DimensionCalculatorFactoryInterface;
use Guillaumetissier\ImageResizer\DimensionCalculator\DimensionCalculatorInterface;
use Guillaumetissier\ImageResizer\DimensionReader\DimensionsReaderInterface;
use Guillaumetissier\ImageResizer\Exceptions\InvalidPathException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use Guillaumetissier\ImageResizer\ImageResizer;
use Guillaumetissier\ImageResizer\ImageResizer\ImageResizerFactoryInterface;
use Guillaumetissier\ImageResizer\ImageResizer\ImageResizerInterface;
use Guillaumetissier\ImageResizer\ImageResizerConfig;
use Guillaumetissier\ImageResizer\Validators\HeightValidator;
use Guillaumetissier\ImageResizer\Validators\InterlaceValidator;
use Guillaumetissier\ImageResizer\Validators\QualityValidator;
use Guillaumetissier\ImageResizer\Validators\RatioValidator;
use Guillaumetissier\ImageResizer\Validators\ScaleModeValidator;
use Guillaumetissier\ImageResizer\Validators\SourceFileValidator;
use Guillaumetissier\ImageResizer\Validators\TargetDirValidator;
use Guillaumetissier\ImageResizer\Validators\ValidatorFactoryInterface;
use Guillaumetissier\ImageResizer\Validators\ValidatorInterface;
use Guillaumetissier\ImageResizer\Validators\WidthValidator;
use Guillaumetissier\PathUtilities\Path;
use PHPUnit\Framework\TestCase;

final class ImageResizerTest extends TestCase
{
    private ValidatorFactoryInterface $validatorFactory;

    private DimensionsReaderInterface $dimensionsReader;

    private DimensionCalculatorFactoryInterface $calculatorFactory;

    private ImageResizerFactoryInterface $imageResizerFactory;

    private ImageResizerConfig $config;

    private string $testDir;

    protected function setUp(): void
    {
        $this->validatorFactory = $this->createMock(ValidatorFactoryInterface::class);
        $this->dimensionsReader = $this->createMock(DimensionsReaderInterface::class);
        $this->calculatorFactory = $this->createMock(DimensionCalculatorFactoryInterface::class);
        $this->imageResizerFactory = $this->createMock(ImageResizerFactoryInterface::class);
        $this->config = ImageResizerConfig::safe();

        // Create test directory and file
        $this->testDir = sys_get_temp_dir().'/image-resizer-test-'.uniqid();
        mkdir($this->testDir, 0755, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testDir)) {
            array_map('unlink', glob("$this->testDir/*"));
            rmdir($this->testDir);
        }
    }

    private function createImageResizer(?ImageResizerConfig $config = null): ImageResizer
    {
        $reflection = new \ReflectionClass(ImageResizer::class);
        $constructor = $reflection->getConstructor();
        $constructor->setAccessible(true);

        $instance = $reflection->newInstanceWithoutConstructor();
        $constructor->invoke(
            $instance,
            $this->validatorFactory,
            $this->dimensionsReader,
            $this->calculatorFactory,
            $this->imageResizerFactory,
            $config ?? $this->config
        );

        return $instance;
    }

    public function testCreateFactoryMethod(): void
    {
        $resizer = ImageResizer::create();

        $this->assertInstanceOf(ImageResizer::class, $resizer);
    }

    public function testCreateFactoryMethodWithCustomConfig(): void
    {
        $customConfig = ImageResizerConfig::strict();
        $resizer = ImageResizer::create($customConfig);

        $this->assertInstanceOf(ImageResizer::class, $resizer);
    }

    public function testSetTransformationWidth(): void
    {
        $widthValidator = $this->createMock(ValidatorInterface::class);
        $widthValidator->expects($this->once())
            ->method('validate')
            ->with(800);

        $this->validatorFactory->expects($this->once())
            ->method('create')
            ->with(WidthValidator::class, $this->config)
            ->willReturn($widthValidator);

        $resizer = $this->createImageResizer();
        $result = $resizer->setTransformation(Transformations::SET_WIDTH, 800);

        $this->assertSame($resizer, $result, 'Should return self for method chaining');
    }

    public function testSetTransformationHeight(): void
    {
        $heightValidator = $this->createMock(ValidatorInterface::class);
        $heightValidator->expects($this->once())
            ->method('validate')
            ->with(600);

        $this->validatorFactory->expects($this->once())
            ->method('create')
            ->with(HeightValidator::class, $this->config)
            ->willReturn($heightValidator);

        $resizer = $this->createImageResizer();
        $result = $resizer->setTransformation(Transformations::SET_HEIGHT, 600);

        $this->assertSame($resizer, $result, 'Should return self for method chaining');
    }

    public function testSetTransformationRatio(): void
    {
        $ratioValidator = $this->createMock(ValidatorInterface::class);
        $ratioValidator->expects($this->once())
            ->method('validate')
            ->with(1.5);

        $this->validatorFactory->expects($this->once())
            ->method('create')
            ->with(RatioValidator::class, $this->config)
            ->willReturn($ratioValidator);

        $resizer = $this->createImageResizer();
        $result = $resizer->setTransformation(Transformations::SET_RATIO, 1.5);

        $this->assertSame($resizer, $result, 'Should return self for method chaining');
    }

    public function testSetTransformationThrowsExceptionOnInvalidValue(): void
    {
        $widthValidator = $this->createMock(ValidatorInterface::class);
        $widthValidator->expects($this->once())
            ->method('validate')
            ->with(-100)
            ->willThrowException(InvalidRangeException::outOfRange('width', -100, 10, 8000));

        $this->validatorFactory->expects($this->once())
            ->method('create')
            ->with(WidthValidator::class, $this->config)
            ->willReturn($widthValidator);

        $this->expectException(InvalidRangeException::class);

        $resizer = $this->createImageResizer();
        $resizer->setTransformation(Transformations::SET_WIDTH, -100);
    }

    public function testSetTransformations(): void
    {
        $widthValidator = $this->createMock(ValidatorInterface::class);
        $widthValidator->expects($this->once())->method('validate')->with(1200);

        $heightValidator = $this->createMock(ValidatorInterface::class);
        $heightValidator->expects($this->once())->method('validate')->with(800);

        $this->validatorFactory->expects($this->exactly(2))
            ->method('create')
            ->willReturnMap([
                [WidthValidator::class, $this->config, $widthValidator],
                [HeightValidator::class, $this->config, $heightValidator],
            ]);

        $resizer = $this->createImageResizer();
        $result = $resizer->setTransformations([
            Transformations::SET_WIDTH->value => 1200,
            Transformations::SET_HEIGHT->value => 800,
        ]);

        $this->assertSame($resizer, $result, 'Should return self for method chaining');
    }

    public function testSetOptionQuality(): void
    {
        $qualityValidator = $this->createMock(ValidatorInterface::class);
        $qualityValidator->expects($this->once())
            ->method('validate')
            ->with(85);

        $this->validatorFactory->expects($this->once())
            ->method('create')
            ->with(QualityValidator::class, $this->config)
            ->willReturn($qualityValidator);

        $resizer = $this->createImageResizer();
        $result = $resizer->setOption(Options::QUALITY, 85);

        $this->assertSame($resizer, $result, 'Should return self for method chaining');
    }

    public function testSetOptionInterlace(): void
    {
        $interlaceValidator = $this->createMock(ValidatorInterface::class);
        $interlaceValidator->expects($this->once())
            ->method('validate')
            ->with(true);

        $this->validatorFactory->expects($this->once())
            ->method('create')
            ->with(InterlaceValidator::class, $this->config)
            ->willReturn($interlaceValidator);

        $resizer = $this->createImageResizer();
        $result = $resizer->setOption(Options::INTERLACE, true);

        $this->assertSame($resizer, $result, 'Should return self for method chaining');
    }

    public function testSetOptionScaleMode(): void
    {
        $validator = $this->createMock(ScaleModeValidator::class);
        $validator->expects($this->once())
            ->method('validate')
            ->with('fit');

        $this->validatorFactory->expects($this->once())
            ->method('create')
            ->with(ScaleModeValidator::class, $this->config)
            ->willReturn($validator);

        $resizer = $this->createImageResizer();
        $result = $resizer->setOption(Options::SCALE_MODE, 'fit');

        $this->assertSame($resizer, $result, 'Should return self for method chaining');
    }

    public function testSetOptions(): void
    {
        $qualityValidator = $this->createMock(ValidatorInterface::class);
        $qualityValidator->expects($this->once())->method('validate')->with(90);

        $interlaceValidator = $this->createMock(ValidatorInterface::class);
        $interlaceValidator->expects($this->once())->method('validate')->with(true);

        $this->validatorFactory->expects($this->exactly(2))
            ->method('create')
            ->willReturnMap([
                [QualityValidator::class, $this->config, $qualityValidator],
                [InterlaceValidator::class, $this->config, $interlaceValidator],
            ]);

        $resizer = $this->createImageResizer();
        $result = $resizer->setOptions([
            Options::QUALITY->value => 90,
            Options::INTERLACE->value => true,
        ]);

        $this->assertSame($resizer, $result, 'Should return self for method chaining');
    }

    public function testResizeValidatesSourceFile(): void
    {
        $sourceFile = $this->testDir.'/source.jpg';
        file_put_contents($sourceFile, 'fake image data');

        $sourceFileValidator = $this->createMock(ValidatorInterface::class);
        $sourceFileValidator->expects($this->once())
            ->method('validate')
            ->with($this->callback(function ($path) use ($sourceFile) {
                return $path instanceof Path && $path->absolutePath() === $sourceFile;
            }));

        $this->validatorFactory->expects($this->once())
            ->method('create')
            ->with(SourceFileValidator::class, $this->config)
            ->willReturn($sourceFileValidator);

        // Mock other dependencies
        $oldDimensions = new ImageDimensions(1000, 800);
        $newDimensions = new ImageDimensions(500, 400);

        $this->dimensionsReader->expects($this->once())
            ->method('readDimensions')
            ->willReturn($oldDimensions);

        $calculator = $this->createMock(DimensionCalculatorInterface::class);
        $calculator->expects($this->once())
            ->method('calculateDimensions')
            ->willReturn($newDimensions);

        $this->calculatorFactory->expects($this->once())
            ->method('create')
            ->willReturn($calculator);

        $imageResizer = $this->createMock(ImageResizerInterface::class);
        $imageResizer->expects($this->once())
            ->method('resize');

        $this->imageResizerFactory->expects($this->once())
            ->method('create')
            ->willReturn($imageResizer);

        $resizer = $this->createImageResizer();
        $resizer->resize($sourceFile);
    }

    public function testResizeThrowsExceptionWhenSourceFileInvalid(): void
    {
        $sourceFile = $this->testDir.'/nonexistent.jpg';

        $sourceFileValidator = $this->createMock(ValidatorInterface::class);
        $sourceFileValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(InvalidPathException::notFound(new Path($this->testDir.'/nonexistent.jpg')));

        $this->validatorFactory->expects($this->once())
            ->method('create')
            ->with(SourceFileValidator::class, $this->config)
            ->willReturn($sourceFileValidator);

        $this->expectException(InvalidPathException::class);

        $resizer = $this->createImageResizer();
        $resizer->resize($sourceFile);
    }

    public function testResizeValidatesTargetDirectoryWhenTargetDoesNotExist(): void
    {
        $sourceFile = $this->testDir.'/source.jpg';
        $targetFile = $this->testDir.'/output/target.jpg';
        file_put_contents($sourceFile, 'fake image data');

        $sourceFileValidator = $this->createMock(ValidatorInterface::class);
        $targetDirValidator = $this->createMock(ValidatorInterface::class);
        $targetDirValidator->expects($this->once())
            ->method('validate');

        $this->validatorFactory->expects($this->exactly(2))
            ->method('create')
            ->willReturnMap([
                [SourceFileValidator::class, $this->config, $sourceFileValidator],
                [TargetDirValidator::class, $this->config, $targetDirValidator],
            ]);

        // Mock other dependencies
        $oldDimensions = new ImageDimensions(1000, 800);
        $newDimensions = new ImageDimensions(500, 400);

        $this->dimensionsReader->expects($this->once())
            ->method('readDimensions')
            ->willReturn($oldDimensions);

        $calculator = $this->createMock(DimensionCalculatorInterface::class);
        $calculator->expects($this->once())
            ->method('calculateDimensions')
            ->willReturn($newDimensions);

        $this->calculatorFactory->expects($this->once())
            ->method('create')
            ->willReturn($calculator);

        $imageResizer = $this->createMock(ImageResizerInterface::class);

        $this->imageResizerFactory->expects($this->once())
            ->method('create')
            ->willReturn($imageResizer);

        $resizer = $this->createImageResizer();
        $resizer->resize($sourceFile, $targetFile);
    }

    public function testResizeWithExistingTargetDoesNotValidateDirectory(): void
    {
        $sourceFile = $this->testDir.'/source.jpg';
        $targetFile = $this->testDir.'/target.jpg';
        file_put_contents($sourceFile, 'fake image data');
        file_put_contents($targetFile, 'existing target');

        $sourceFileValidator = $this->createMock(ValidatorInterface::class);

        // Should only validate source, not target directory since target exists
        $this->validatorFactory->expects($this->once())
            ->method('create')
            ->with(SourceFileValidator::class, $this->config)
            ->willReturn($sourceFileValidator);

        // Mock other dependencies
        $oldDimensions = new ImageDimensions(1000, 800);
        $newDimensions = new ImageDimensions(500, 400);

        $this->dimensionsReader->expects($this->once())
            ->method('readDimensions')
            ->willReturn($oldDimensions);

        $calculator = $this->createMock(DimensionCalculatorInterface::class);
        $calculator->expects($this->once())
            ->method('calculateDimensions')
            ->willReturn($newDimensions);

        $this->calculatorFactory->expects($this->once())
            ->method('create')
            ->willReturn($calculator);

        $imageResizer = $this->createMock(ImageResizerInterface::class);

        $this->imageResizerFactory->expects($this->once())
            ->method('create')
            ->willReturn($imageResizer);

        $resizer = $this->createImageResizer();
        $resizer->resize($sourceFile, $targetFile);
    }

    public function testMethodChainingWithMultipleOperations(): void
    {
        $widthValidator = $this->createMock(ValidatorInterface::class);
        $heightValidator = $this->createMock(ValidatorInterface::class);
        $qualityValidator = $this->createMock(ValidatorInterface::class);
        $interlaceValidator = $this->createMock(ValidatorInterface::class);

        $this->validatorFactory->expects($this->exactly(4))
            ->method('create')
            ->willReturnMap([
                [WidthValidator::class, $this->config, $widthValidator],
                [HeightValidator::class, $this->config, $heightValidator],
                [QualityValidator::class, $this->config, $qualityValidator],
                [InterlaceValidator::class, $this->config, $interlaceValidator],
            ]);

        $resizer = $this->createImageResizer();
        $result = $resizer
            ->setTransformation(Transformations::SET_WIDTH, 1920)
            ->setTransformation(Transformations::SET_HEIGHT, 1080)
            ->setOption(Options::QUALITY, 90)
            ->setOption(Options::INTERLACE, true);

        $this->assertSame($resizer, $result, 'Should support method chaining');
    }

    public function testUsesCustomConfigForValidation(): void
    {
        $customConfig = ImageResizerConfig::strict();

        $widthValidator = $this->createMock(ValidatorInterface::class);
        $widthValidator->expects($this->once())->method('validate');

        $this->validatorFactory->expects($this->once())
            ->method('create')
            ->with(WidthValidator::class, $customConfig)
            ->willReturn($widthValidator);

        $resizer = $this->createImageResizer($customConfig);
        $resizer->setTransformation(Transformations::SET_WIDTH, 1000);
    }

    public function testResizePassesConfigToImageResizerFactory(): void
    {
        $sourceFile = $this->testDir.'/source.jpg';
        file_put_contents($sourceFile, 'fake image data');

        $sourceFileValidator = $this->createMock(ValidatorInterface::class);

        $this->validatorFactory->expects($this->once())
            ->method('create')
            ->willReturn($sourceFileValidator);

        $oldDimensions = new ImageDimensions(1000, 800);
        $newDimensions = new ImageDimensions(500, 400);

        $this->dimensionsReader->expects($this->once())
            ->method('readDimensions')
            ->willReturn($oldDimensions);

        $calculator = $this->createMock(DimensionCalculatorInterface::class);
        $calculator->expects($this->once())
            ->method('calculateDimensions')
            ->willReturn($newDimensions);

        $this->calculatorFactory->expects($this->once())
            ->method('create')
            ->willReturn($calculator);

        $imageResizer = $this->createMock(ImageResizerInterface::class);

        // Verify that config is passed to factory
        $this->imageResizerFactory->expects($this->once())
            ->method('create')
            ->with(
                $this->isInstanceOf(Path::class),
                $this->identicalTo($this->config),
                $this->isType('array')
            )
            ->willReturn($imageResizer);

        $resizer = $this->createImageResizer();
        $resizer->resize($sourceFile);
    }
}
