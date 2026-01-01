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
use Guillaumetissier\ImageResizer\Exceptions\DirNotFoundException;
use Guillaumetissier\ImageResizer\Exceptions\DirNotWritableException;
use Guillaumetissier\ImageResizer\ImageResizer\ImageResizerFactory;
use Guillaumetissier\ImageResizer\ImageResizer\ImageResizerFactoryInterface;
use Guillaumetissier\PathUtilities\Path;

final class ImageResizer
{
    private static ?ImageResizer $instance = null;

    private ResizeType $type = ResizeType::PROPORTIONAL;

    protected array $transformations = [];

    protected array $options = [];

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self(
                new DimensionsReader(),
                new DimensionCalculatorFactory(),
                new ImageResizerFactory()
            );
        }

        return self::$instance;
    }

    public function __construct(
        private readonly DimensionsReaderInterface $imageDimensionsReader,
        private readonly DimensionCalculatorFactoryInterface $dimensionCalculatorFactory,
        private readonly ImageResizerFactoryInterface $imageResizerFactory,
    ) {
    }

    public function setType(ResizeType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param array{
     *     setHeight?: int,
     *     setWidth?: int,
     *     setRatio?: int|float
     * } $transformations
     */
    public function setTransformations(array $transformations): self
    {
        $this->transformations = $transformations;

        return $this;
    }

    public function setTransformation(Transformations $transformation, mixed $value): self
    {
        $this->transformations[$transformation->value] = $value;

        return $this;
    }

    /**
     * @param array{
     *     mode?: string,
     *     quality?: int,
     *     interlace?: bool
     * } $options
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function setOption(Options $option, mixed $value): self
    {
        $this->options[$option->value] = $value;

        return $this;
    }

    public function resize(string $source, ?string $target = null): void
    {
        $sourcePath = new Path($source);
        if (null !== $target) {
            $targetPath = new Path($target);
            if (!$targetPath->exists()) {
                $this->checkDirectory($targetPath->parent());
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

    private function checkDirectory(Path $target): void
    {
        if (!$target->isDir()) {
            throw new DirNotFoundException($target);
        }
        if (!$target->permissions()->isWritable()) {
            throw new DirNotWritableException($target);
        }
    }

    private function addResizedPrefix(Path $file): Path
    {
        return new Path($file->dirname().DIRECTORY_SEPARATOR.'resized-'.$file->basename());
    }
}
