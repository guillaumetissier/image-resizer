<?php

namespace Guillaumetissier\ImageResizer\Validators;

interface ValidatorInterface
{
    public static function validate(mixed $value): void;
}
