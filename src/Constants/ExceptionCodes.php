<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Constants;

enum ExceptionCodes: int
{
    case CANNOT_CREATE_IMAGE = 0;
    case CANNOT_READ_IMAGE_SIZE = 1;
    case CANNOT_SAVE_IMAGE = 2;
    case INVALID_FORMAT = 4;
    case INVALID_PATH = 5;
    case INVALID_QUALITY = 6;
    case INVALID_RANGE = 7;
    case INVALID_TARGET_EXTENSION = 8;
    case INVALID_TYPE = 9;
    case MISSING_KEY = 10;
    case UNSUPPORTED_IMAGE_TYPE = 11;
}
