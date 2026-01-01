<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Constants;

enum Options: string
{
    case INTERLACE = 'interlace';
    case QUALITY = 'quality';
    case SCALE_MODE = 'mode';
}
