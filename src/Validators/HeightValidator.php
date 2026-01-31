<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Validators;

use Guillaumetissier\ImageResizer\ImageResizerConfig;

final class HeightValidator extends BaseSizeValidator
{
    public function __construct(ImageResizerConfig $config)
    {
        parent::__construct('height', $config->minHeight, $config->maxHeight);
    }
}
