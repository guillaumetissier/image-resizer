<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Constants;

enum ExceptionCodes: int
{
    case CANNOT_CREATE_IMAGE = 0;
    case CANNOT_READ_IMAGE_SIZE = 1;
    case CANNOT_SAVE_IMAGE = 2;
    case DIR_NOT_FOUND = 3;
    case DIR_NOT_WRITABLE = 4;
    case FILE_NOT_FOUND = 5;
    case FILE_NOT_WRITABLE = 6;
    case INVALID_TARGET_EXTENSION = 7;
    case MISSING_TRANSFORMATION_KEY = 8;
    case UNSUPPORTED_IMAGE_TYPE = 9;
    case WRONG_VALUE_TYPE = 10;
}
