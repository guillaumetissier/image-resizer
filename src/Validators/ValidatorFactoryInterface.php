<?php

namespace Guillaumetissier\ImageResizer\Validators;

use Guillaumetissier\ImageResizer\ImageResizerConfig;

interface ValidatorFactoryInterface
{
    public function create(string $validatorName, ImageResizerConfig $config): ValidatorInterface;
}
