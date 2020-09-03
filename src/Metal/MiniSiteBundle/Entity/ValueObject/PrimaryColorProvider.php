<?php

namespace Metal\MiniSiteBundle\Entity\ValueObject;

class PrimaryColorProvider
{
    protected static $types = array(
        '#f16961' => array(
            'color' => '#f16961',
            'input_css_class' => 'f16961',
        ),
        '#e18141' => array(
            'color' => '#e18141',
            'input_css_class' => 'e18141',
        ),
        '#c7902c' => array(
            'color' => '#c7902c',
            'input_css_class' => 'c7902c',
        ),
        '#84a324' => array(
            'color' => '#84a324',
            'input_css_class' => '84a324',
        ),
        '#3fab61' => array(
            'color' => '#3fab61',
            'input_css_class' => '3fab61',
        ),
        '#18a3d1' => array(
            'color' => '#18a3d1',
            'input_css_class' => '18a3d1',
        ),
        '#9f8cd1' => array(
            'color' => '#9f8cd1',
            'input_css_class' => '9f8cd1',
        ),
        '#e06eb1' => array(
            'color' => '#e06eb1',
            'input_css_class' => 'e06eb1',
        ),
        '#919191' => array(
            'color' => '#919191',
            'input_css_class' => '919191',
        )
    );

    public static function getAllTypes()
    {
        return self::$types;
    }

    public static function getAllTypesAsSimpleArray()
    {
        return array_map(
            function ($type) {
                return $type['color'];
            },
            self::$types
        );
    }

}
