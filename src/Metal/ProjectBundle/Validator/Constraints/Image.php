<?php

namespace Metal\ProjectBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Image as BaseImage;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Image extends BaseImage
{
    public function __construct($options = null)
    {
        if (!is_array($options)) {
            $options = array();
        }

        parent::__construct(
            array_replace(
                array(
                    'maxSize' => 10 * 1024 * 1024,
                    'maxHeight' => 6000,
                    'maxWidth' => 6000,
                    'mimeTypes' => array(
                        'image/bmp',
                        'image/gif',
                        'image/jpeg',
                        'image/jpg',
                        'image/pjpeg',
                        'image/png',
                        'image/tiff',
                        'image/x-png'
                    )
                ),
                $options
            )
        );
    }

    public function validatedBy()
    {
         return MetalImageValidator::class;
    }
}
