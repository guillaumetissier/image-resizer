<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Constants;

enum ResizeType: string
{
    case PROPORTIONAL = 'proportional';
    case FIXED = 'fixed';
    case FIXED_HEIGHT = 'fixed_height';
    case FIXED_WIDTH = 'fixed_width';
}
