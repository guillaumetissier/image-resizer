<?php

namespace Guillaumetissier\ImageResizer\Constants;

enum SupportedExtension: string
{
    case GIF = 'gif';
    case JPEG = 'jpeg';
    case JPG = 'jpg';
    case PNG = 'png';

    public static function all(): array
    {
        return [
            self::GIF->value,
            self::JPEG->value,
            self::JPG->value,
            self::PNG->value,
        ];
    }
}
