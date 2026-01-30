# ImageResizer

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Latest Version](https://img.shields.io/badge/version-1.1.0-orange.svg)](CHANGELOG.md)

A modern, type-safe PHP library for resizing images with comprehensive validation and a fluent interface.

## ✨ Features

- 🎯 **Multiple resize modes**: Proportional, fixed width, fixed height, exact dimensions
- 🔒 **Comprehensive validation**: Early parameter validation with clear error messages
- 🖼️ **Format support**: JPEG, PNG, GIF
- ⚡ **Quality control**: Adjustable compression quality for lossy formats
- 🔗 **Fluent interface**: Chainable methods for easy configuration
- 🛡️ **Type safety**: Full PHP 8.1+ type hints and enum support
- 📝 **Well tested**: Comprehensive test coverage

## 📋 Requirements

- PHP 8.1 or higher
- GD extension or Imagick
- [guillaumetissier/path-utilities](https://github.com/guillaumetissier/path-utilities) ^1.0

## 📦 Installation

```bash
composer require guillaumetissier/image-resizer
```

## 🚀 Quick Start

```php
<?php

use Guillaumetissier\ImageResizer\ImageResizer;
use Guillaumetissier\ImageResizer\Constants\ResizeType;
use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\Constants\Options;

// Resize to 50% of original size
$resizer = ImageResizer::create()
    ->setResizeType(ResizeType::PROPORTIONAL)
    ->setTransformation(Transformations::SET_RATIO, 0.5)
    ->setOption(Options::QUALITY, 85);

$resizer->resize('input.jpg', 'output.jpg');
```

## 📖 Usage

### Resize Modes

#### Proportional Resize (by ratio)

```php
use Guillaumetissier\ImageResizer\ImageResizer;
use Guillaumetissier\ImageResizer\Constants\ResizeType;
use Guillaumetissier\ImageResizer\Constants\Transformations;

$resizer = ImageResizer::create()
    ->setResizeType(ResizeType::PROPORTIONAL)
    ->setTransformation(Transformations::SET_RATIO, 0.5); // 50% of original

$resizer->resize('photo.jpg', 'photo-small.jpg');
```

#### Fixed Width (maintain aspect ratio)

```php
$resizer = ImageResizer::create()
    ->setResizeType(ResizeType::FIXED_WIDTH)
    ->setTransformation(Transformations::SET_WIDTH, 800);

$resizer->resize('photo.jpg', 'photo-800w.jpg');
```

#### Fixed Height (maintain aspect ratio)

```php
$resizer = ImageResizer::create()
    ->setResizeType(ResizeType::FIXED_HEIGHT)
    ->setTransformation(Transformations::SET_HEIGHT, 600);

$resizer->resize('photo.jpg', 'photo-600h.jpg');
```

#### Exact Dimensions (may distort)

```php
$resizer = ImageResizer::create()
    ->setResizeType(ResizeType::EXACT)
    ->setTransformation(Transformations::SET_WIDTH, 800)
    ->setTransformation(Transformations::SET_HEIGHT, 600);

$resizer->resize('photo.jpg', 'photo-800x600.jpg');
```

### Output Options

#### Quality Control

```php
use Guillaumetissier\ImageResizer\Constants\Options;

// High quality (larger file)
$resizer->setOption(Options::QUALITY, 95);

// Medium quality (balanced)
$resizer->setOption(Options::QUALITY, 80);

// Low quality (smaller file)
$resizer->setOption(Options::QUALITY, 60);
```

#### Progressive JPEG

```php
$resizer->setOption(Options::INTERLACE, true);
```

#### Multiple Options

```php
$resizer->setOptions([
    Options::QUALITY->value => 85,
    Options::INTERLACE->value => true,
]);
```

### Auto-generated Output Path

If you don't specify an output path, the library automatically generates one with a `resized-` prefix:

```php
$resizer->resize('photo.jpg'); // Creates: resized-photo.jpg
```

## 🎨 Advanced Examples

### Thumbnail Generator

```php
$resizer = ImageResizer::create()
    ->setResizeType(ResizeType::PROPORTIONAL)
    ->setTransformation(Transformations::SET_RATIO, 0.2)
    ->setOption(Options::QUALITY, 75);

$resizer->resize('large-photo.jpg', 'thumbnail.jpg');
```

### Batch Processing

```php
$images = ['photo1.jpg', 'photo2.jpg', 'photo3.jpg'];

$resizer = ImageResizer::create()
    ->setResizeType(ResizeType::FIXED_WIDTH)
    ->setTransformation(Transformations::SET_WIDTH, 1200)
    ->setOption(Options::QUALITY, 85);

foreach ($images as $image) {
    $resizer->resize($image, 'resized/' . basename($image));
}
```

### Responsive Image Set

```php
$sizes = [
    'thumbnail' => 150,
    'small' => 480,
    'medium' => 768,
    'large' => 1200,
];

$resizer = ImageResizer::create()
    ->setResizeType(ResizeType::FIXED_WIDTH)
    ->setOption(Options::QUALITY, 80);

foreach ($sizes as $name => $width) {
    $resizer->setTransformation(Transformations::SET_WIDTH, $width);
    $resizer->resize('original.jpg', "output-{$name}.jpg");
}
```

## ✅ Validation

The library validates all parameters before processing, providing clear error messages:

### Dimension Validation

```php
// ✓ Valid: 10-2000 pixels
$resizer->setTransformation(Transformations::SET_WIDTH, 800);

// ✗ Throws InvalidRangeException: Dimension out of range
$resizer->setTransformation(Transformations::SET_WIDTH, 5000);

// ✗ Throws InvalidTypeException: Must be integer
$resizer->setTransformation(Transformations::SET_WIDTH, "800");
```

### Quality Validation

```php
// ✓ Valid: 0-100
$resizer->setOption(Options::QUALITY, 85);

// ✗ Throws InvalidRangeException: Quality out of range
$resizer->setOption(Options::QUALITY, 150);

// ✗ Throws InvalidTypeException: Must be integer
$resizer->setOption(Options::QUALITY, 85.5);
```

### Ratio Validation

```php
// ✓ Valid: 0.01-2.0
$resizer->setTransformation(Transformations::SET_RATIO, 0.5);

// ✗ Throws InvalidRangeException: Ratio out of range
$resizer->setTransformation(Transformations::SET_RATIO, 5.0);

// ✗ Throws InvalidTypeException: Must be numeric
$resizer->setTransformation(Transformations::SET_RATIO, "0.5");
```

### File Validation

```php
// ✗ Throws InvalidPathException: File not found
$resizer->resize('missing.jpg', 'output.jpg');

// ✗ Throws InvalidPathException: Unsupported format
$resizer->resize('document.pdf', 'output.jpg');

// ✗ Throws InvalidPathException: Directory not writable
$resizer->resize('input.jpg', '/root/output.jpg');
```

## 🔧 Supported Formats

| Format | Extensions | Notes |
|--------|-----------|-------|
| **JPEG** | `.jpg`, `.jpeg` | Supports quality setting |
| **PNG** | `.png` | Lossless, ignores quality setting |
| **GIF** | `.gif` | Supports transparency |

## ⚙️ Configuration Limits

| Parameter | Minimum | Maximum | Default |
|-----------|---------|---------|---------|
| **Width** | 10px | 2000px | - |
| **Height** | 10px | 2000px | - |
| **Quality** | 0 | 100 | 80 |
| **Ratio** | 0.01 (1%) | 2.0 (200%) | - |

These limits are designed to prevent:
- 🚫 Memory exhaustion from extremely large images
- 🚫 Unusably small output images
- 🚫 File system issues

## 🛡️ Error Handling

```php
use Guillaumetissier\ImageResizer\Exceptions\InvalidTypeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException;
use Guillaumetissier\ImageResizer\Exceptions\InvalidPathException;

try {
    $resizer = ImageResizer::create()
        ->setTransformation(Transformations::SET_WIDTH, 800)
        ->setOption(Options::QUALITY, 85);
    
    $resizer->resize('input.jpg', 'output.jpg');
    
    echo "✓ Image resized successfully\n";
    
} catch (InvalidTypeException $e) {
    // Wrong parameter type (e.g., string instead of int)
    echo "Type error: {$e->getMessage()}\n";
    
} catch (InvalidRangeException $e) {
    // Parameter outside acceptable range
    echo "Range error: {$e->getMessage()}\n";
    
} catch (InvalidPathException $e) {
    // File/directory issues (not found, not readable, etc.)
    echo "Path error: {$e->getMessage()}\n";
    
} catch (\Exception $e) {
    // Other errors
    echo "Error: {$e->getMessage()}\n";
}
```

## 📚 API Reference

### ImageResizer

#### Factory Method

```php
public static function create(): self
```

Creates a new ImageResizer instance with default dependencies.

#### Configuration Methods

```php
public function setResizeType(ResizeType $type): self
public function setTransformation(Transformations $transformation, int|float $value): self
public function setTransformations(array $transformations): self
public function setOption(Options $option, int|bool|string $value): self
public function setOptions(array $options): self
```

#### Processing Method

```php
public function resize(string $source, ?string $target = null): void
```

Resizes the image from `$source` to `$target`. If `$target` is null, generates filename with `resized-` prefix.

**Throws:**
- `InvalidTypeException` - Wrong parameter type
- `InvalidRangeException` - Parameter out of range
- `InvalidPathException` - File/directory issues

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards

- Follow PSR-12 coding standard
- Add tests for new features
- Update documentation as needed

```bash
# Run code style fixer
./vendor/bin/php-cs-fixer fix

# Run static analysis
./vendor/bin/phpstan analyze
```

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🔗 Links

- [Repository](https://github.com/guillaumetissier/image-resizer)
- [Issue Tracker](https://github.com/guillaumetissier/image-resizer/issues)
- [Changelog](CHANGELOG.md)

## 💡 Tips

### Memory Management

For very large images, consider adjusting PHP's memory limit:

```php
ini_set('memory_limit', '256M');
```

### Performance

- Use lower quality settings (60-80) for web images
- Enable progressive JPEG for faster perceived loading


## 🙏 Acknowledgments

- Built with [path-utilities](https://github.com/guillaumetissier/path-utilities)
- Inspired by best practices in modern PHP development

---

Made with ❤️ by [Guillaume Tissier](https://github.com/guillaumetissier)