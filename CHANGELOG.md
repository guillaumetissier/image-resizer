# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.1] - 2026-01-27

### Fixed
- Add missing image type validation in DimensionsReader
- Prevent processing of unsupported image formats

### Added
- InvalidImageTypeException for clearer error messages when using unsupported formats

## [1.1.0] - 2026-01-27

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