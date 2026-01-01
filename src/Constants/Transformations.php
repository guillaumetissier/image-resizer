<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Constants;

enum Transformations: string
{
    case SET_RATIO = 'setRatio';
    case SET_WIDTH = 'setWidth';
    case SET_HEIGHT = 'setHeight';
}
