<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer;

/**
 * Configuration for ImageResizer.
 *
 * All values are automatically constrained to valid ranges.
 * Invalid values are silently adjusted to nearest valid value.
 */
final class ImageResizerConfig
{
    private const ABSOLUTE_MIN_DIMENSION = 1;
    private const ABSOLUTE_MAX_DIMENSION = 50000;
    private const ABSOLUTE_MIN_RATIO = 0.01;
    private const ABSOLUTE_MAX_RATIO = 10.0;
    private const ABSOLUTE_MIN_QUALITY = 0;
    private const ABSOLUTE_MAX_QUALITY = 100;

    private const DEFAULT_QUALITY = 80;
    private const DEFAULT_MIN = 100;
    private const DEFAULT_MAX = 2000;

    public ?int $minWidth;

    public ?int $maxWidth;

    public ?int $minHeight;

    public ?int $maxHeight;

    public ?float $minRatio;

    public ?float $maxRatio;

    public ?int $minQuality;

    public ?int $maxQuality;

    public int $defaultQuality;

    public bool $defaultInterlace;

    /**
     * Create a new ImageResizerConfig.
     *
     * @param array{
     *     minWidth?: int|null,
     *     maxWidth?: int|null,
     *     minHeight?: int|null,
     *     maxHeight?: int|null,
     *     minRatio?: float|null,
     *     maxRatio?: float|null,
     *     minQuality?: int|null,
     *     maxQuality?: int|null,
     *     defaultQuality?: int,
     *     defaultInterlace?: bool
     * } $config Configuration array
     *
     * @example
     * $config = new ImageResizerConfig([
     *     'maxWidth' => 4000,
     *     'maxHeight' => 4000,
     *     'defaultQuality' => 85,
     * ]);
     */
    public function __construct(array $config = [])
    {
        // Constrain dimensions
        $this->minWidth = $this->constrainDimension($config['minWidth'] ?? self::DEFAULT_MIN);
        $this->maxWidth = $this->constrainDimension($config['maxWidth'] ?? self::DEFAULT_MAX);
        $this->minHeight = $this->constrainDimension($config['minHeight'] ?? self::DEFAULT_MIN);
        $this->maxHeight = $this->constrainDimension($config['maxHeight'] ?? self::DEFAULT_MAX);

        // Ensure min <= max for dimensions
        if (null !== $this->minWidth && null !== $this->maxWidth && $this->minWidth > $this->maxWidth) {
            [$this->minWidth, $this->maxWidth] = [$this->maxWidth, $this->minWidth];
        }
        if (null !== $this->minHeight && null !== $this->maxHeight && $this->minHeight > $this->maxHeight) {
            [$this->minHeight, $this->maxHeight] = [$this->maxHeight, $this->minHeight];
        }

        // Constrain ratios
        $this->minRatio = $this->constrainRatio($config['minRatio'] ?? self::ABSOLUTE_MIN_RATIO);
        $this->maxRatio = $this->constrainRatio($config['maxRatio'] ?? self::ABSOLUTE_MAX_RATIO);

        // Ensure min <= max for ratio
        if (null !== $this->minRatio && null !== $this->maxRatio && $this->minRatio > $this->maxRatio) {
            [$this->minRatio, $this->maxRatio] = [$this->maxRatio, $this->minRatio];
        }

        // Constrain qualities
        $this->minQuality = $this->constrainQuality($config['minQuality'] ?? self::ABSOLUTE_MIN_QUALITY);
        $this->maxQuality = $this->constrainQuality($config['maxQuality'] ?? self::ABSOLUTE_MAX_QUALITY);
        $this->defaultQuality = $this->constrainQuality($config['defaultQuality'] ?? self::DEFAULT_QUALITY);

        // Ensure min <= max for quality
        if (null !== $this->minQuality && null !== $this->maxQuality && $this->minQuality > $this->maxQuality) {
            [$this->minQuality, $this->maxQuality] = [$this->maxQuality, $this->minQuality];
        }

        // Ensure defaultQuality is within min/max
        if (null !== $this->minQuality && $this->defaultQuality < $this->minQuality) {
            $this->defaultQuality = $this->minQuality;
        }
        if (null !== $this->maxQuality && $this->defaultQuality > $this->maxQuality) {
            $this->defaultQuality = $this->maxQuality;
        }

        $this->defaultInterlace = $config['defaultInterlace'] ?? false;
    }

    /**
     * Constrain dimension to valid range.
     */
    private function constrainDimension(int $value): int
    {
        return max(self::ABSOLUTE_MIN_DIMENSION, min($value, self::ABSOLUTE_MAX_DIMENSION));
    }

    /**
     * Constrain ratio to valid range.
     */
    private function constrainRatio(float $value): float
    {
        return max(self::ABSOLUTE_MIN_RATIO, min($value, self::ABSOLUTE_MAX_RATIO));
    }

    /**
     * Constrain quality to valid range.
     */
    private function constrainQuality(int $value): int
    {
        return max(self::ABSOLUTE_MIN_QUALITY, min($value, self::ABSOLUTE_MAX_QUALITY));
    }

    /**
     * Create default config (no constraints).
     */
    public static function default(): self
    {
        return new self();
    }

    /**
     * Create safe config with reasonable constraints.
     *
     * Suitable for user-uploaded images on a public website.
     */
    public static function safe(): self
    {
        return new self([
            'minWidth' => 10,
            'maxWidth' => 8000,
            'minHeight' => 10,
            'maxHeight' => 8000,
            'minRatio' => 0.1,
            'maxRatio' => 2.0,
            'minQuality' => 50,
            'maxQuality' => 95,
            'defaultQuality' => 85,
        ]);
    }

    /**
     * Create strict config for high-security environments.
     */
    public static function strict(): self
    {
        return new self([
            'minWidth' => 100,
            'maxWidth' => 4000,
            'minHeight' => 100,
            'maxHeight' => 4000,
            'minRatio' => 0.5,
            'maxRatio' => 1.0,
            'minQuality' => 60,
            'maxQuality' => 90,
            'defaultQuality' => 80,
        ]);
    }

    /**
     * Create config optimized for thumbnails.
     */
    public static function thumbnail(): self
    {
        return new self([
            'maxWidth' => 500,
            'maxHeight' => 500,
            'minQuality' => 70,
            'maxQuality' => 85,
            'defaultQuality' => 75,
        ]);
    }

    /**
     * Create config optimized for web images.
     */
    public static function web(): self
    {
        return new self([
            'maxWidth' => 2000,
            'maxHeight' => 2000,
            'minQuality' => 70,
            'maxQuality' => 90,
            'defaultQuality' => 80,
        ]);
    }

    /**
     * Create config optimized for print quality.
     */
    public static function print(): self
    {
        return new self([
            'maxWidth' => 10000,
            'maxHeight' => 10000,
            'minQuality' => 85,
            'maxQuality' => 100,
            'defaultQuality' => 95,
        ]);
    }

    /**
     * Merge this config with another config.
     *
     * Values from $override take precedence.
     *
     * @param array $override Configuration to merge
     *
     * @return self New config with merged values
     *
     * @example
     * $base = ImageResizerConfig::safe();
     * $custom = $base->merge(['maxWidth' => 5000]);
     */
    public function merge(array $override): self
    {
        return new self(array_merge([
            'minWidth' => $this->minWidth,
            'maxWidth' => $this->maxWidth,
            'minHeight' => $this->minHeight,
            'maxHeight' => $this->maxHeight,
            'minRatio' => $this->minRatio,
            'maxRatio' => $this->maxRatio,
            'minQuality' => $this->minQuality,
            'maxQuality' => $this->maxQuality,
            'defaultQuality' => $this->defaultQuality,
            'defaultInterlace' => $this->defaultInterlace,
        ], $override));
    }

    /**
     * Convert config to array.
     *
     * @return array{
     *     minWidth: int|null,
     *     maxWidth: int|null,
     *     minHeight: int|null,
     *     maxHeight: int|null,
     *     minRatio: float|null,
     *     maxRatio: float|null,
     *     minQuality: int|null,
     *     maxQuality: int|null,
     *     defaultQuality: int,
     *     defaultInterlace: bool
     * }
     */
    public function toArray(): array
    {
        return [
            'minWidth' => $this->minWidth,
            'maxWidth' => $this->maxWidth,
            'minHeight' => $this->minHeight,
            'maxHeight' => $this->maxHeight,
            'minRatio' => $this->minRatio,
            'maxRatio' => $this->maxRatio,
            'minQuality' => $this->minQuality,
            'maxQuality' => $this->maxQuality,
            'defaultQuality' => $this->defaultQuality,
            'defaultInterlace' => $this->defaultInterlace,
        ];
    }
}
