# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-01-31

### ⚠️ BREAKING CHANGES

#### Constructor is now private
The `ImageResizer` constructor is now private. You **must** use the factory method `ImageResizer::create()` to instantiate the class.

**Migration:**
```php
// ❌ No longer possible
$resizer = new ImageResizer(...);

// ✅ Use the factory method instead
$resizer = ImageResizer::create();

// ✅ With custom configuration
$resizer = ImageResizer::create(ImageResizerConfig::safe());
```

**Rationale:** This change ensures consistent initialization and allows internal implementation changes without breaking the public API.

### Added
- **New**: `ImageResizerConfig` class for configurable validation constraints
  - Customize min/max values for width, height, ratio, and quality
  - Set default quality and interlace behavior
- **New**: Pre-configured profiles for common use cases:
  - `ImageResizerConfig::default()` - Standard configuration
  - `ImageResizerConfig::safe()` - For public websites with user uploads
  - `ImageResizerConfig::strict()` - For high-security environments
  - `ImageResizerConfig::thumbnail()` - Optimized for thumbnails
  - `ImageResizerConfig::web()` - Optimized for web images
  - `ImageResizerConfig::print()` - Optimized for print quality
- **New**: `ValidatorFactory` and `ValidatorFactoryInterface` for better dependency injection
- **New**: Specific validators for improved separation of concerns:
  - `WidthValidator` - Validates width transformations
  - `HeightValidator` - Validates height transformations
  - `InterlaceValidator` - Validates interlace option
  - `ScaleModeValidator` - Validates scale mode option
  - `TargetDirValidator` - Validates target directory
- **New**: Exception helper methods:
  - `InvalidRangeException::outOfSet()` - For set validation errors
  - `InvalidTypeException::notImageDimensions()` - For type checking
- **New**: Optional configuration parameter in factory method:
  - `ImageResizer::create(?ImageResizerConfig $config = null)`

### Changed
- **Constructor is now private** - use `ImageResizer::create()` instead
- Validators refactored from static methods to instance-based (internal improvement)
- Properties `$transformations` and `$options` visibility changed from `protected` to `private`
- Improved validation error messages for better debugging
- PHPDoc updated across all public methods
- `ImageResizerFactory::create()` now receives `ImageResizerConfig` parameter

### Removed
- Public constructor (use factory method instead)
- Private helper methods `validateBoolean()` and `validateDirectory()` (replaced by dedicated validators)

### Fixed
- Improved validation consistency across the library
- Better error context in exception messages
- Proper validation of target directories

---

## Usage Guide

### Basic Usage

```php
use Guillaumetissier\ImageResizer\ImageResizer;
use Guillaumetissier\ImageResizer\Constants\Transformations;
use Guillaumetissier\ImageResizer\Constants\Options;

// Create resizer instance
$resizer = ImageResizer::create();

// Set transformations
$resizer
    ->setTransformation(Transformations::SET_WIDTH, 1920)
    ->setTransformation(Transformations::SET_HEIGHT, 1080)
    ->setOption(Options::QUALITY, 85)
    ->setOption(Options::INTERLACE, true);

// Resize image
$resizer->resize('source.jpg', 'output.jpg');
```

### Using Pre-configured Profiles

```php
// Safe configuration for public websites (max 8000x8000, quality 50-95)
$resizer = ImageResizer::create(ImageResizerConfig::safe());

// Strict configuration for secure environments (max 4000x4000, quality 60-90)
$resizer = ImageResizer::create(ImageResizerConfig::strict());

// Thumbnail configuration (max 500x500, quality 70-85)
$resizer = ImageResizer::create(ImageResizerConfig::thumbnail());

// Web configuration (max 2000x2000, quality 70-90)
$resizer = ImageResizer::create(ImageResizerConfig::web());

// Print configuration (max 10000x10000, quality 85-100)
$resizer = ImageResizer::create(ImageResizerConfig::print());
```

### Custom Configuration

```php
use Guillaumetissier\ImageResizer\ImageResizerConfig;

$config = new ImageResizerConfig([
    'minWidth' => 100,
    'maxWidth' => 3000,
    'minHeight' => 100,
    'maxHeight' => 3000,
    'minRatio' => 0.5,
    'maxRatio' => 2.0,
    'minQuality' => 60,
    'maxQuality' => 95,
    'defaultQuality' => 85,
    'defaultInterlace' => true,
]);

$resizer = ImageResizer::create($config);
```

### Method Chaining

```php
ImageResizer::create(ImageResizerConfig::web())
    ->setTransformation(Transformations::SET_WIDTH, 1920)
    ->setOption(Options::QUALITY, 90)
    ->setOption(Options::INTERLACE, true)
    ->resize('input.jpg', 'output.jpg');
```

### Multiple Transformations

```php
$resizer = ImageResizer::create()
    ->setTransformations([
        Transformations::SET_WIDTH => 1920,
        Transformations::SET_HEIGHT => 1080,
    ])
    ->setOptions([
        Options::QUALITY => 85,
        Options::INTERLACE => true,
    ]);

$resizer->resize('source.jpg', 'output.jpg');
```

---

## Configuration Options

### ImageResizerConfig Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `minWidth` | `?int` | `100` | Minimum allowed width in pixels |
| `maxWidth` | `?int` | `2000` | Maximum allowed width in pixels |
| `minHeight` | `?int` | `100` | Minimum allowed height in pixels |
| `maxHeight` | `?int` | `2000` | Maximum allowed height in pixels |
| `minRatio` | `?float` | `0.01` | Minimum aspect ratio |
| `maxRatio` | `?float` | `10.0` | Maximum aspect ratio |
| `minQuality` | `?int` | `0` | Minimum JPEG quality (0-100) |
| `maxQuality` | `?int` | `100` | Maximum JPEG quality (0-100) |
| `defaultQuality` | `int` | `80` | Default quality if not specified |
| `defaultInterlace` | `bool` | `false` | Default interlace mode |

### Pre-configured Profiles

| Profile | Use Case | Width | Height | Quality | Ratio |
|---------|----------|-------|--------|---------|-------|
| `default()` | Standard usage | 100-2000 | 100-2000 | 0-100 | 0.01-10.0 |
| `safe()` | Public websites | 10-8000 | 10-8000 | 50-95 | 0.1-2.0 |
| `strict()` | Secure environments | 100-4000 | 100-4000 | 60-90 | 0.5-1.0 |
| `thumbnail()` | Thumbnails | ≤500 | ≤500 | 70-85 | - |
| `web()` | Web images | ≤2000 | ≤2000 | 70-90 | - |
| `print()` | Print quality | ≤10000 | ≤10000 | 85-100 | - |

---

## Migration from v1.x

### No changes needed if using factory method

If your code already uses `ImageResizer::create()`, no changes are required:

```php
// This works in both v1.x and v2.0
$resizer = ImageResizer::create();
$resizer->resize('input.jpg', 'output.jpg');
```

### Direct instantiation no longer supported

If you were instantiating with `new` (unlikely), you must update:

```php
// ❌ v1.x - no longer works
$resizer = new ImageResizer(
    $dimensionsReader,
    $calculatorFactory,
    $imageResizerFactory
);

// ✅ v2.0 - use factory method
$resizer = ImageResizer::create();

// ✅ v2.0 - with custom config
$resizer = ImageResizer::create(ImageResizerConfig::strict());
```

[Your license here]

## [1.1.1] - 2026-01-30

### Fixed
- Add missing image type validation in DimensionsReader
- Prevent processing of unsupported image formats

### Added
- InvalidImageTypeException for clearer error messages when using unsupported formats

## [1.1.0] - 2026-01-30

### Added

#### Validators
- **DimensionValidator**: Validates image dimensions (width/height) are integers within range 10-2000px
- **QualityValidator**: Validates JPEG quality is an integer between 0-100
- **RatioValidator**: Validates scaling ratio is numeric and within range 0.01-2.0
- **SourceFileValidator**: Validates source file exists, is readable, and has supported format (jpg, jpeg, png, gif)
- **TargetDirValidator**: Validates target directory exists and is writable

#### Exceptions
- **InvalidTypeException**: Thrown when parameter has wrong type (e.g., string instead of int)
- **InvalidRangeException**: Thrown when numeric parameter is outside acceptable range
- **InvalidPathException**: Thrown when file/directory path is invalid or has wrong permissions

#### Utilities
- **EnumKeyedArray**: Wrapper class for arrays using BackedEnum values as keys, eliminating need for `->value` everywhere
- **ValidatorInterface**: Common interface implemented by all validators

#### Constants
- **Quality**: Enum defining quality constants (MIN=0, MAX=100)
- **SupportedExtension**: Enum listing supported image formats

### Changed
- **ImageResizer**: Added comprehensive parameter validation for all transformations and options
    - Validates dimension parameters (width, height) are positive integers within limits
    - Validates ratio parameter is numeric and within acceptable range
    - Validates quality parameter is integer between 0-100
    - Validates source file exists, is readable, and has supported format
    - Validates target directory exists and is writable
- Improved error messages with specific details about validation failures
- All validation happens early before any image processing begins

### Security
- Added path validation to prevent directory traversal attacks
- Added file permission checks to ensure safe file operations
- Added format validation to prevent processing of unsupported file types

## [1.0.0] - 2026-01-15

### Added
- Initial release
- Image resizing with multiple modes (proportional, fixed width, fixed height, exact)
- Support for JPEG, PNG, GIF formats
- Quality and interlace options for output images
- Fluent interface for easy configuration

[1.1.0]: https://github.com/guillaumetissier/image-resizer/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/guillaumetissier/image-resizer/releases/tag/v1.0.0