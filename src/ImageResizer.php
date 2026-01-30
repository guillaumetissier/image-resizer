<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer;

use Guillaumetissier\ImageResizer\Constants\Options;
use Guillaumetissier\ImageResizer\Constants\ResizeType;
use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\DimensionCalculator\DimensionCalculatorFactory;
use Guillaumetissier\ImageResizer\DimensionCalculator\DimensionCalculatorFactoryInterface;
use Guillaumetissier\ImageResizer\DimensionReader\DimensionsReader;
use Guillaumetissier\ImageResizer\DimensionReader\DimensionsReaderInterface;
use Guillaumetissier\ImageResizer\Exceptions\InvalidPathException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\ImageResizer\ImageResizerFactory;
use Guillaumetissier\ImageResizer\ImageResizer\ImageResizerFactoryInterface;
use Guillaumetissier\ImageResizer\Validators\DimensionValidator;
use Guillaumetissier\ImageResizer\Validators\QualityValidator;
use Guillaumetissier\ImageResizer\Validators\RatioValidator;
use Guillaumetissier\ImageResizer\Validators\SourceFileValidator;
use Guillaumetissier\PathUtilities\Path;

final class ImageResizer
{
    private ResizeType $type = ResizeType::PROPORTIONAL;

    protected array $transformations = [];

    protected array $options = [];

    public static function create(): self
    {
        return new self(
            new DimensionsReader(),
            new DimensionCalculatorFactory(),
            new ImageResizerFactory()
        );
    }

    public function __construct(
        private readonly DimensionsReaderInterface $imageDimensionsReader,
        private readonly DimensionCalculatorFactoryInterface $dimensionCalculatorFactory,
        private readonly ImageResizerFactoryInterface $imageResizerFactory,
    ) {
    }

    /**
     * Set the resize type (proportional, fixed width, fixed height, exact).
     *
     * @return self For method chaining
     */
    public function setResizeType(ResizeType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Alias for setResizeType() for backward compatibility.
     */
    public function setType(ResizeType $type): self
    {
        return $this->setResizeType($type);
    }

    /**
     * Set multiple transformations at once.
     *
     * @param array{
     *     setHeight?: int,
     *     setWidth?: int,
     *     setRatio?: int|float
     * } $transformations
     *
     * @return self For method chaining
     *
     * @throws InvalidTypeException If value has wrong type
     * @throws InvalidRangeException If value is out of range
     */
    public function setTransformations(array $transformations): self
    {
        foreach ($transformations as $key => $value) {
            $transformation = Transformations::from($key);
            $this->setTransformation($transformation, $value);
        }

        return $this;
    }

    /**
     * Set a single transformation.
     *
     * @param Transformations $transformation The transformation type
     * @param mixed           $value          The transformation value (dimension in pixels or ratio)
     *
     * @return self For method chaining
     *
     * @throws InvalidTypeException If value has wrong type
     * @throws InvalidRangeException If value is out of range
     */
    public function setTransformation(Transformations $transformation, mixed $value): self
    {
        match ($transformation) {
            Transformations::SET_WIDTH,
            Transformations::SET_HEIGHT => DimensionValidator::validate($value),
            Transformations::SET_RATIO => RatioValidator::validate($value),
        };

        $this->transformations[$transformation->value] = $value;

        return $this;
    }

    /**
     * Set multiple options at once.
     *
     * @param array{
     *     mode?: string,
     *     quality?: int,
     *     interlace?: bool
     * } $options
     *
     * @return self For method chaining
     *
     * @throws \InvalidArgumentException|InvalidTypeException If option values are invalid
     */
    public function setOptions(array $options): self
    {
        foreach ($options as $key => $value) {
            $option = Options::from($key);
            $this->setOption($option, $value);
        }

        return $this;
    }

    /**
     * Set a single option.
     *
     * @param Options         $option The option type
     * @param int|bool|string $value  The option value
     *
     * @return self For method chaining
     *
     * @throws InvalidTypeException If value is invalid for a quality option
     */
    public function setOption(Options $option, int|bool|string $value): self
    {
        match ($option) {
            Options::QUALITY => QualityValidator::validate($value),
            Options::INTERLACE => $this->validateBoolean($value),
            default => null, // No validation for other options
        };

        $this->options[$option->value] = $value;

        return $this;
    }

    /**
     * Resize an image from source to target.
     *
     * @param string      $source Path to the source image
     * @param string|null $target Path to the target image (auto-generated if null)
     *
     * @throws InvalidTypeException
     * @throws InvalidPathException If source file doesn't exist
     */
    public function resize(string $source, ?string $target = null): void
    {
        $sourcePath = new Path($source);

        SourceFileValidator::validate($sourcePath);

        if (null !== $target) {
            $targetPath = new Path($target);
            if (!$targetPath->exists()) {
                $this->validateDirectory($targetPath->parent());
            }
        } else {
            $targetPath = $this->addResizedPrefix($sourcePath);
        }

        $oldDimensions = $this->imageDimensionsReader
            ->readDimensions($sourcePath);

        $newDimensions = $this->dimensionCalculatorFactory
            ->create($this->type, $this->transformations)
            ->calculateDimensions($oldDimensions);

        $this->imageResizerFactory
            ->create($sourcePath, $this->options)
            ->resize($sourcePath, $targetPath, $newDimensions);
    }

    private function validateBoolean(mixed $value): void
    {
        if (!is_bool($value)) {
            throw new \InvalidArgumentException('Option value must be a boolean, got: '.gettype($value));
        }
    }

    private function validateDirectory(Path $target): void
    {
    }

    private function addResizedPrefix(Path $file): Path
    {
        return new Path($file->dirname().DIRECTORY_SEPARATOR.'resized-'.$file->basename());
    }
}
