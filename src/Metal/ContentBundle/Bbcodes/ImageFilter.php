<?php

namespace Metal\ContentBundle\Bbcodes;

use Decoda\Filter\ImageFilter as BaseImageFilter;
use FM\BbcodeBundle\Decoda\Decoda;

class ImageFilter extends BaseImageFilter
{
    const IMAGE_PATTERN_NETCAT = '/^((?:https?:\/)?(?:\.){0,2}\/)((?:.*?)(\.(jpg|jpeg|png|gif|bmp))?)(\?[^#]+)?(#[\-\w]+)?$/is';

    /**
     * Supported tags.
     *
     * @type array
     */
    protected $_tags = array(
        'img' => array(
            'htmlTag' => 'img',
            'displayType' => Decoda::TYPE_INLINE,
            'allowedTypes' => Decoda::TYPE_NONE,
            'contentPattern' => self::IMAGE_PATTERN_NETCAT,
            'autoClose' => true,
            'attributes' => array(
                'default' => self::WIDTH_HEIGHT,
                'width' => self::DIMENSION,
                'height' => self::DIMENSION,
                'alt' => self::WILDCARD
            )
        ),
        'image' => array(
            'aliasFor' => 'img'
        )
    );
}
