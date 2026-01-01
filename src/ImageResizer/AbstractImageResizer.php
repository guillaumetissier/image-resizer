<?php

declare(strict_types=1);

namespace Guillaumetissier\ImageResizer\ImageResizer;

use Guillaumetissier\ImageResizer\Constants\Options;
use Guillaumetissier\ImageResizer\Exceptions\FileNotWritableException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidExtensionException;
use Guillaumetissier\ImageResizer\ImageDimensions;
use Guillaumetissier\PathUtilities\Path;

abstract class AbstractImageResizer implements ImageResizerInterface
{
    protected ?\GdImage $source = null;

    protected ?\GdImage $target = null;

    /**
     * @param array{
     *     mode?: string,
     *     interlace?: bool,
     *     quality?: int,
     * } $options
     */
    public function __construct(private readonly array $options)
    {
    }

    public function resize(Path $source, Path $target, ImageDimensions $newDimensions): void
    {
        try {
            $this->checkTarget($target, $this->extractExtension($source));
            $this->setSource((string) $source);
            $this->scaleImage($newDimensions);
            $this->interlaceImage();
            $this->save((string) $target);
        } finally {
            $this->cleanup();
        }
    }

    private function checkTarget(Path $target, string $expectedExt): void
    {
        if ($target->isFile() && !$target->permissions()->isWritable()) {
            throw new FileNotWritableException($target);
        }

        if ($expectedExt !== $this->extractExtension($target)) {
            throw new InvalidExtensionException($target, $expectedExt);
        }
    }

    abstract protected function setSource(string $source): void;

    private function scaleImage(ImageDimensions $newDimensions): void
    {
        $this->target = @imagescale(
            $this->source,
            $newDimensions->getWidth(),
            $newDimensions->getHeight(),
            $this->getOption(Options::SCALE_MODE, IMG_BILINEAR_FIXED)
        );
    }

    private function interlaceImage(): void
    {
        @imageinterlace(
            $this->target,
            $this->getOption(Options::INTERLACE, false)
        );
    }

    abstract protected function save(string $target): void;

    private function cleanup(): void
    {
        foreach (['source', 'target'] as $prop) {
            if ($this->$prop instanceof \GdImage) {
                @imagedestroy($this->$prop);
                $this->$prop = null;
            }
        }
    }

    protected function getOption(Options $optionKey, mixed $default): mixed
    {
        return $this->options[$optionKey->value] ?? $default;
    }

    private function extractExtension(Path $path): string
    {
        return ('jpeg' === ($ext = $path->extension())) ? 'jpg' : $ext;
    }
}
