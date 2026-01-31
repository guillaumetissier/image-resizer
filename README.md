# ImageResizer

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

A powerful and flexible PHP library for resizing images with configurable validation constraints.

## Features

- 🎯 **Simple API** - Intuitive fluent interface for easy image manipulation
- ⚙️ **Configurable** - Customizable validation constraints for width, height, ratio, and quality
- 🛡️ **Secure** - Pre-configured profiles for different security requirements
- 🧪 **Well-tested** - Comprehensive test coverage
- 🔧 **Extensible** - Clean architecture with dependency injection
- 📦 **Multiple formats** - Support for JPEG, PNG, and GIF

## Requirements

- PHP 8.1 or higher
- GD extension or Imagick extension

## Installation

Install via Composer:

```bash
composer require guillaumetissier/image-resizer
```

## Quick Start

```php
use Guillaumetissier\ImageResizer\ImageResizer;
use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\Constants\Options;

// Create resizer with safe configuration
$resizer = ImageResizer::create();

// Resize image
$resizer
    ->setTransformation(Transformations::SET_WIDTH, 1920)
    ->setOption(Options::QUALITY, 85)
    ->resize('source.jpg', 'output.jpg');
```

## Usage

### Basic Resizing

```php
use Guillaumetissier\ImageResizer\ImageResizer;
use Guillaumetissier\ImageResizer\Constants\Transformations;

$resizer = ImageResizer::create();

// Resize by width (maintains aspect ratio)
$resizer->setTransformation(Transformations::SET_WIDTH, 800)
    ->resize('image.jpg', 'resized.jpg');

// Resize by height (maintains aspect ratio)
$resizer->setTransformation(Transformations::SET_HEIGHT, 600)
    ->resize('image.jpg', 'resized.jpg');

// Resize by aspect ratio
$resizer->setTransformation(Transformations::SET_RATIO, 16/9)
    ->resize('image.jpg', 'resized.jpg');
```

### Multiple Transformations

```php
$resizer->setTransformations([
    Transformations::SET_WIDTH->value => 1920,
    Transformations::SET_HEIGHT->value => 1080,
]);

$resizer->resize('source.jpg', 'output.jpg');
```

### Quality and Options

```php
use Guillaumetissier\ImageResizer\Constants\Options;

$resizer = ImageResizer::create()
    ->setOption(Options::QUALITY, 85)           // JPEG quality (0-100)
    ->setOption(Options::INTERLACE, true);      // Progressive JPEG

// Or set multiple options at once
$resizer->setOptions([
    Options::QUALITY->value => 90,
    Options::INTERLACE->value => true,
]);

$resizer->resize('image.jpg', 'output.jpg');
```

### Method Chaining

All setter methods return `$this` for fluent interface:

```php
ImageResizer::create()
    ->setTransformation(Transformations::SET_WIDTH, 1920)
    ->setTransformation(Transformations::SET_HEIGHT, 1080)
    ->setOption(Options::QUALITY, 85)
    ->setOption(Options::INTERLACE, true)
    ->resize('input.jpg', 'output.jpg');
```

## Configuration Profiles

ImageResizer comes with pre-configured validation profiles for different use cases.

### Available Profiles

```php
use Guillaumetissier\ImageResizer\ImageResizerConfig;

// Default configuration
$resizer = ImageResizer::create(ImageResizerConfig::default());

// Safe configuration - for public websites with user uploads
$resizer = ImageResizer::create(ImageResizerConfig::safe());

// Strict configuration - for high-security environments
$resizer = ImageResizer::create(ImageResizerConfig::strict());

// Thumbnail configuration - optimized for small images
$resizer = ImageResizer::create(ImageResizerConfig::thumbnail());

// Web configuration - optimized for web display
$resizer = ImageResizer::create(ImageResizerConfig::web());

// Print configuration - optimized for print quality
$resizer = ImageResizer::create(ImageResizerConfig::print());
```

### Profile Specifications

| Profile | Max Width | Max Height | Quality Range | Ratio Range | Use Case |
|---------|-----------|------------|---------------|-------------|----------|
| `default()` | 2000px | 2000px | 0-100 | 0.01-10.0 | Standard usage |
| `safe()` | 8000px | 8000px | 50-95 | 0.1-2.0 | Public websites |
| `strict()` | 4000px | 4000px | 60-90 | 0.5-1.0 | Secure environments |
| `thumbnail()` | 500px | 500px | 70-85 | - | Thumbnails |
| `web()` | 2000px | 2000px | 70-90 | - | Web images |
| `print()` | 10000px | 10000px | 85-100 | - | Print quality |

### Custom Configuration

Create your own configuration with specific constraints:

```php
use Guillaumetissier\ImageResizer\ImageResizerConfig;

$config = new ImageResizerConfig([
    'minWidth' => 100,              // Minimum width in pixels
    'maxWidth' => 3000,             // Maximum width in pixels
    'minHeight' => 100,             // Minimum height in pixels
    'maxHeight' => 3000,            // Maximum height in pixels
    'minRatio' => 0.5,              // Minimum aspect ratio (width/height)
    'maxRatio' => 2.0,              // Maximum aspect ratio
    'minQuality' => 60,             // Minimum JPEG quality (0-100)
    'maxQuality' => 95,             // Maximum quality
    'defaultQuality' => 85,         // Default quality if not specified
    'defaultInterlace' => true,     // Enable progressive JPEG by default
]);

$resizer = ImageResizer::create($config);
```

## Validation

ImageResizer validates all inputs to ensure safe and consistent image processing.

### Automatic Validation

All transformations and options are automatically validated:

```php
try {
    $resizer = ImageResizer::create(ImageResizerConfig::safe());
    
    // This will throw InvalidRangeException if width > 8000
    $resizer->setTransformation(Transformations::SET_WIDTH, 10000);
    
} catch (\Guillaumetissier\ImageResizer\Exceptions\InvalidRangeException $e) {
    echo "Validation error: " . $e->getMessage();
}
```

### Common Exceptions

- `InvalidRangeException` - Value is out of allowed range
- `InvalidTypeException` - Value has wrong type
- `InvalidPathException` - File path is invalid or file doesn't exist

### Validation Examples

```php
use Guillaumetissier\ImageResizer\ImageResizerConfig;

$config = ImageResizerConfig::strict();  // Max width: 4000px
$resizer = ImageResizer::create($config);

// ✅ Valid - within limits
$resizer->setTransformation(Transformations::SET_WIDTH, 2000);

// ❌ Invalid - exceeds max width
$resizer->setTransformation(Transformations::SET_WIDTH, 5000);
// Throws: InvalidRangeException

// ❌ Invalid - wrong type
$resizer->setTransformation(Transformations::SET_WIDTH, "2000");
// Throws: InvalidTypeException

// ❌ Invalid - file doesn't exist
$resizer->resize('nonexistent.jpg', 'output.jpg');
// Throws: InvalidPathException
```

## Examples

### Example 1: User Avatar Processing

```php
use Guillaumetissier\ImageResizer\ImageResizer;
use Guillaumetissier\ImageResizer\ImageResizerConfig;
use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\Constants\Options;

// Strict validation for user uploads
$resizer = ImageResizer::create(ImageResizerConfig::strict());

try {
    $resizer
        ->setTransformations([
            Transformations::SET_WIDTH => 200,
            Transformations::SET_HEIGHT => 200,
        ])
        ->setOptions([
            Options::QUALITY => 85,
            Options::INTERLACE => true,
        ])
        ->resize($_FILES['avatar']['tmp_name'], 'uploads/avatars/user-123.jpg');
        
    echo "Avatar uploaded successfully!";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Example 2: Batch Processing

```php
$resizer = ImageResizer::create(ImageResizerConfig::web())
    ->setTransformation(Transformations::SET_WIDTH, 1920)
    ->setOption(Options::QUALITY, 85);

$images = glob('originals/*.jpg');

foreach ($images as $image) {
    $filename = basename($image);
    try {
        $resizer->resize($image, "processed/{$filename}");
        echo "Processed: {$filename}\n";
    } catch (\Exception $e) {
        echo "Error processing {$filename}: {$e->getMessage()}\n";
    }
}
```

### Example 3: Creating Thumbnails

```php
$resizer = ImageResizer::create(ImageResizerConfig::thumbnail())
    ->setOptions([
        Options::QUALITY => 75,
        Options::INTERLACE => false,
    ]);

// Create multiple thumbnail sizes
$sizes = [
    'small' => 150,
    'medium' => 300,
    'large' => 500,
];

foreach ($sizes as $name => $width) {
    $resizer->setTransformation(Transformations::SET_WIDTH, $width)
        ->resize('original.jpg', "thumbnails/{$name}.jpg");
}
```

## Architecture

ImageResizer follows SOLID principles and uses dependency injection for flexibility:

```
ImageResizer (main class)
├── ValidatorFactory (creates validators with config)
│   ├── WidthValidator
│   ├── HeightValidator
│   ├── RatioValidator
│   ├── QualityValidator
│   └── ...
├── DimensionsReader (reads image dimensions)
├── DimensionCalculatorFactory (calculates new dimensions)
└── ImageResizerFactory (creates format-specific resizers)
    ├── JpegImageResizer
    ├── PngImageResizer
    └── GifImageResizer
```

## API Reference

### ImageResizer

#### Factory Method

```php
public static function create(?ImageResizerConfig $config = null): self
```

Creates a new ImageResizer instance with optional configuration.

#### Methods

```php
// Set a single transformation
public function setTransformation(Transformations $transformation, mixed $value): self

// Set multiple transformations at once
public function setTransformations(array $transformations): self

// Set a single option
public function setOption(Options $option, int|bool|string $value): self

// Set multiple options at once
public function setOptions(array $options): self

// Resize the image
public function resize(string $source, ?string $target = null): void
```

### ImageResizerConfig

#### Factory Methods

```php
public static function default(): self
public static function safe(): self
public static function strict(): self
public static function thumbnail(): self
public static function web(): self
public static function print(): self
```

#### Constructor

```php
public function __construct(array $config = [])
```

**Available parameters:**
- `minWidth` (int|null) - Minimum width in pixels
- `maxWidth` (int|null) - Maximum width in pixels
- `minHeight` (int|null) - Minimum height in pixels
- `maxHeight` (int|null) - Maximum height in pixels
- `minRatio` (float|null) - Minimum aspect ratio
- `maxRatio` (float|null) - Maximum aspect ratio
- `minQuality` (int|null) - Minimum quality (0-100)
- `maxQuality` (int|null) - Maximum quality (0-100)
- `defaultQuality` (int) - Default quality value
- `defaultInterlace` (bool) - Default interlace setting

### Constants

#### Transformations

```php
Transformations::SET_WIDTH   // Resize by width
Transformations::SET_HEIGHT  // Resize by height
Transformations::SET_RATIO   // Resize by aspect ratio
```

#### Options

```php
Options::QUALITY      // Image quality (0-100)
Options::INTERLACE    // Progressive/interlaced (bool)
Options::SCALE_MODE   // Scaling mode (integer)
```

## Migration from v1.x

### Breaking Changes

The constructor is now private. Use the factory method instead:

```php
// ❌ v1.x - No longer works
$resizer = new ImageResizer(...);

// ✅ v2.0 - Use factory method
$resizer = ImageResizer::create();
$resizer = ImageResizer::create(ImageResizerConfig::safe());
```

### No Changes Needed

If you were already using `ImageResizer::create()`, no changes are required:

```php
// This works in both v1.x and v2.0
$resizer = ImageResizer::create();
$resizer->setTransformation(Transformations::SET_WIDTH, 800);
$resizer->resize('input.jpg', 'output.jpg');
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

- 📧 Email: [guillaume.tissier@yahoo.com]
- 🐛 Issues: [GitHub Issues](https://github.com/guillaumetissier/image-resizer/issues)
- 📖 Documentation: [Full Documentation](https://github.com/guillaumetissier/image-resizer/wiki)

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a detailed list of changes.

## Credits

Created and maintained by [Guillaume Tissier](https://github.com/guillaumetissier)
