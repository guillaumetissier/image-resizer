<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\Constants;

/**
 * Centralized mapping between image file extensions and PHP IMAGETYPE_* constants.
 */
final class ImageType
{
    private const EXT_TO_IMAGE_TYPE = [
        'gif' => IMAGETYPE_GIF,
        'jpeg' => IMAGETYPE_JPEG,
        'jpg' => IMAGETYPE_JPEG,
        'png' => IMAGETYPE_PNG,
    ];

    private const IMAGE_TYPE_TO_EXT = [
        IMAGETYPE_GIF => 'gif',
        IMAGETYPE_JPEG => 'jpeg',
        IMAGETYPE_PNG => 'png',
    ];

    /**
     * Get all supported IMAGETYPE_* constants.
     *
     * @return array<int> List of IMAGETYPE_* values (e.g., [1, 2, 3])
     */
    public static function all(): array
    {
        return array_keys(self::IMAGE_TYPE_TO_EXT);
    }

    /**
     * Get all supported file extensions.
     *
     * @return array<string> List of extensions (e.g., ['gif', 'jpeg', 'jpg', 'png'])
     */
    public static function allExtensions(): array
    {
        return array_keys(self::EXT_TO_IMAGE_TYPE);
    }

    /**
     * Convert IMAGETYPE_* constant to file extension.
     *
     * @param int $type IMAGETYPE_* constant
     *
     * @return string File extension (e.g., 'jpeg', 'png', 'gif') or 'unknown'
     *
     * @example
     * ImageType::toExtension(IMAGETYPE_JPEG); // 'jpeg'
     * ImageType::toExtension(IMAGETYPE_PNG);  // 'png'
     * ImageType::toExtension(999);            // 'unknown'
     */
    public static function toExtension(int $type): string
    {
        return self::IMAGE_TYPE_TO_EXT[$type] ?? 'unknown';
    }

    /**
     * Convert file extension to IMAGETYPE_* constant.
     *
     * @param string $extension File extension (e.g., 'jpg', 'png')
     *
     * @return int|null IMAGETYPE_* constant or null if unsupported
     *
     * @example
     * ImageType::toImageType('jpg');  // IMAGETYPE_JPEG (2)
     * ImageType::toImageType('png');  // IMAGETYPE_PNG (3)
     * ImageType::toImageType('bmp');  // null
     */
    public static function toImageType(string $extension): ?int
    {
        return self::EXT_TO_IMAGE_TYPE[$extension] ?? null;
    }
}
