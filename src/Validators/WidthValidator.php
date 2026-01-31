<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Validators;

use Guillaumetissier\ImageResizer\ImageResizerConfig;

final class WidthValidator extends BaseSizeValidator
{
    public function __construct(ImageResizerConfig $config)
    {
        parent::__construct('width', $config->minWidth, $config->maxWidth);
    }
}
