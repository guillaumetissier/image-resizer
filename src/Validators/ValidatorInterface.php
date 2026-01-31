<?php

namespace Guillaumetissier\ImageResizer\Validators;

interface ValidatorInterface
{
    public function validate(mixed $value): void;
}
