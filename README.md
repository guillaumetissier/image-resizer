# ImageResizer

A PHP class that enables you to resize images.

## Installation

Add in your composer.json the following code:

        composer require guillaumetissier/image-resizer

## Usage

### Examples 

In order to resize proportionally (50%) a gif image $source and save the result into $target  

        use Guillaumetissier\ImageResizer\Constants\Options;
        use Guillaumetissier\ImageResizer\Constants\ResizeType;
        use Guillaumetissier\ImageResizer\Constants\Transformations;
        use Guillaumetissier\ImageResizer\ImageResizer as Resizer;

        $resizer = Resizer::getInstance()
            ->setResizeType(ResizeType::PROPORTIONAL)
            ->setTransformation(Transformations::SET_RATIO, 0.5) // 50%
            ->setOption(Options::INTERLACE, true);
        $resizer->resize($source, target);
        
In order to resize a jpeg image $source with a fixed width of 100px and save the result into $target  

        $resizer = Resizer::getInstance()
            ->setResizeType(ResizeType::FIXED_WIDTH)
            ->setTransformation(Transformations::SET_WIDTH, 100) // 100px
            ->setOptions([Options::INTERLACE->value => true, Options::QUALITY->value => 75]);
        $resizer->resize($source, target);
