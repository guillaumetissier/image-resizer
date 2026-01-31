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
use Guillaumetissier\ImageResizer\Validators\HeightValidator;
use Guillaumetissier\ImageResizer\Validators\InterlaceValidator;
use Guillaumetissier\ImageResizer\Validators\QualityValidator;
use Guillaumetissier\ImageResizer\Validators\RatioValidator;
use Guillaumetissier\ImageResizer\Validators\ScaleModeValidator;
use Guillaumetissier\ImageResizer\Validators\SourceFileValidator;
use Guillaumetissier\ImageResizer\Validators\TargetDirValidator;
use Guillaumetissier\ImageResizer\Validators\ValidatorFactory;
use Guillaumetissier\ImageResizer\Validators\ValidatorFactoryInterface;
use Guillaumetissier\ImageResizer\Validators\WidthValidator;
use Guillaumetissier\PathUtilities\Path;

final class ImageResizer
{
    private ResizeType $type = ResizeType::PROPORTIONAL;

    private ImageResizerConfig $config;

    private array $transformations = [];

    private array $options = [];

    public static function create(?ImageResizerConfig $config = null): self
    {
        return new self(
            new ValidatorFactory(),
            new DimensionsReader(),
            new DimensionCalculatorFactory(),
            new ImageResizerFactory(),
            $config ?? ImageResizerConfig::safe()
        );
    }

    private function __construct(
        private readonly ValidatorFactoryInterface $validatorFactory,
        private readonly DimensionsReaderInterface $imageDimensionsReader,
        private readonly DimensionCalculatorFactoryInterface $dimensionCalculatorFactory,
        private readonly ImageResizerFactoryInterface $imageResizerFactory,
        ?ImageResizerConfig $config = null,
    ) {
        $this->config = $config ?? ImageResizerConfig::safe();
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
     * @throws InvalidRangeException If value is out of range
     */
    public function setTransformation(Transformations $transformation, mixed $value): self
    {
        $validator = match ($transformation) {
            Transformations::SET_WIDTH => $this->validatorFactory->create(WidthValidator::class, $this->config),
            Transformations::SET_HEIGHT => $this->validatorFactory->create(HeightValidator::class, $this->config),
            Transformations::SET_RATIO => $this->validatorFactory->create(RatioValidator::class, $this->config),
        };

        $validator->validate($value);
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
     * Set a single resize option.
     *
     * @param Options         $option The option to set
     * @param int|bool|string $value  The option value
     *
     * @return self For method chaining
     *
     * @throws InvalidTypeException  If value type is invalid
     * @throws InvalidRangeException If value is out of range
     */
    public function setOption(Options $option, int|bool|string $value): self
    {
        $validator = match ($option) {
            Options::QUALITY => $this->validatorFactory->create(QualityValidator::class, $this->config),
            Options::INTERLACE => $this->validatorFactory->create(InterlaceValidator::class, $this->config),
            Options::SCALE_MODE => $this->validatorFactory->create(ScaleModeValidator::class, $this->config),
        };

        $validator->validate($value);
        $this->options[$option->value] = $value;

        return $this;
    }

    /**
     * Resize an image from source to target.
     *
     * @param string      $source Path to the source image
     * @param string|null $target Path to the target image (auto-generated if null)
     *
     * @throws InvalidPathException If source file doesn't exist
     */
    public function resize(string $source, ?string $target = null): void
    {
        $sourcePath = new Path($source);

        $this->validatorFactory
            ->create(SourceFileValidator::class, $this->config)
            ->validate($sourcePath);

        if (null !== $target) {
            $targetPath = new Path($target);
            if (!$targetPath->exists()) {
                $this->validatorFactory
                    ->create(TargetDirValidator::class, $this->config)
                    ->validate($targetPath->parent());
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
            ->create($sourcePath, $this->config, $this->options)
            ->resize($sourcePath, $targetPath, $newDimensions);
    }

    private function validateDirectory(Path $target): void
    {
    }

    private function addResizedPrefix(Path $file): Path
    {
        return new Path($file->dirname().DIRECTORY_SEPARATOR.'resized-'.$file->basename());
    }
}
