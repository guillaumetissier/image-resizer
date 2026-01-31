<?php

namespace Guillaumetissier\ImageResizer\Tests\Validators;

use Guillaumetissier\ImageResizer\ImageResizerConfig;
use Guillaumetissier\ImageResizer\Validators\HeightValidator;
use Guillaumetissier\ImageResizer\Validators\InterlaceValidator;
use Guillaumetissier\ImageResizer\Validators\QualityValidator;
use Guillaumetissier\ImageResizer\Validators\RatioValidator;
use Guillaumetissier\ImageResizer\Validators\ScaleModeValidator;
use Guillaumetissier\ImageResizer\Validators\SourceFileValidator;
use Guillaumetissier\ImageResizer\Validators\TargetDirValidator;
use Guillaumetissier\ImageResizer\Validators\ValidatorFactory;
use PHPUnit\Framework\TestCase;

class ValidatorFactoryTest extends TestCase
{
    private ValidatorFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ValidatorFactory();
    }

    /**
     * @dataProvider validateProvider
     */
    public function testValidate(string $validatorName, ImageResizerConfig $config): void
    {
        $this->assertInstanceOf($validatorName, $this->factory->create($validatorName, $config));
    }

    public static function validateProvider(): \Generator
    {
        yield [HeightValidator::class, ImageResizerConfig::safe()];
        yield [QualityValidator::class, ImageResizerConfig::strict()];
        yield [RatioValidator::class, ImageResizerConfig::default()];
        yield [SourceFileValidator::class, ImageResizerConfig::print()];
        yield [TargetDirValidator::class, ImageResizerConfig::thumbnail()];
        yield [InterlaceValidator::class, ImageResizerConfig::print()];
        yield [ScaleModeValidator::class, ImageResizerConfig::web()];
    }
}
