<?php

namespace Guillaumetissier\ImageResizer\Validators;

use Guillaumetissier\ImageResizer\ImageResizerConfig;

final class ValidatorFactory implements ValidatorFactoryInterface
{
    public function create(string $validatorName, ImageResizerConfig $config): ValidatorInterface
    {
        return match ($validatorName) {
            HeightValidator::class => new HeightValidator($config),
            WidthValidator::class => new WidthValidator($config),
            QualityValidator::class => new QualityValidator($config),
            RatioValidator::class => new RatioValidator($config),
            InterlaceValidator::class => new InterlaceValidator(),
            ScaleModeValidator::class => new ScaleModeValidator(),
            SourceFileValidator::class => new SourceFileValidator(),
            TargetDirValidator::class => new TargetDirValidator(),
        };
    }
}
