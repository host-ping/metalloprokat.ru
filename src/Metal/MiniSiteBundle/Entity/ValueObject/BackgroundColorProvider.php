<?php

namespace Metal\MiniSiteBundle\Entity\ValueObject;

use Metal\MiniSiteBundle\Entity\MiniSiteConfig;

class BackgroundColorProvider
{
    protected static $types = array(
        '#cce6d7' => array(
            'color' => '#9fbaac',
            'input_css_class' => '9fbaac',
            'hover_color' => '#daf5e6',
            'bg_color' => '#9fbaac',
            'rgba' => '204,230,215,.2',
            'rgb_hover' => '218,245,230',
            'placeholder' => '#89a496',
            'argb_hover_start' => '#02cce9da',
            'argb_hover_finish' => '#ffcce9da',
            'argb_background_start' => '#02b1cdbe',
            'argb_background_finish' => '#ffb1cdbe'
        ),
        '#e6e0d1' => array(
            'color' => '#ccc6b4',
            'input_css_class' => 'b5af9e',
            'hover_color' => '#f5eedc',
            'bg_color' => '#b5af9e',
            'rgba' => '230,223,209,.2',
            'rgb_hover' => '245,238,220',
            'placeholder' => '#a39d8c',
            'argb_hover_start' => '#02e9e2cf',
            'argb_hover_finish' => '#ffe9e2cf',
            'argb_background_start' => '#02ccc6b4',
            'argb_background_finish' => '#ffccc6b4'
        ),
        '#e1e5eb' => array(
            'color' => '#c2c7d0',
            'input_css_class' => 'acb3bc',
            'hover_color' => '#f0f3fa',
            'bg_color' => '#acb3bc',
            'rgba' => '225,229,235,.2',
            'rgb_hover' => '240,243,250',
            'placeholder' => '#9a9ea7',
            'argb_hover_start' => '#02dfe3ec',
            'argb_hover_finish' => '#ffdfe3ec',
            'argb_background_start' => '#02c2c7d0',
            'argb_background_finish' => '#ffc2c7d0'
        ),
        '#f0dfd1' => array(
            'color' => '#d6c3b2',
            'input_css_class' => 'c7b4a3',
            'hover_color' => '#ffedde',
            'bg_color' => '#c7b4a3',
            'rgba' => '240,224,211,.2',
            'rgb_hover' => '255,238,224',
            'placeholder' => '#ad9a8a',
            'argb_hover_start' => '#02f2dfce',
            'argb_hover_finish' => '#fff2dfce',
            'argb_background_start' => '#02d6c3b2',
            'argb_background_finish' => '#ffd6c3b2'
        ),
        '#d3e7f5' => array(
            'color' => '#b4cadb',
            'input_css_class' => 'a7bccc',
            'hover_color' => '#e0f5ff',
            'bg_color' => '#a7bccc',
            'rgba' => '213,232,245,.2',
            'rgb_hover' => '224,245,255',
            'placeholder' => '#8ca1b1',
            'argb_hover_start' => '#02d0e6f7',
            'argb_hover_finish' => '#ffd0e6f7',
            'argb_background_start' => '#02b4cadb',
            'argb_background_finish' => '#ffb4cadb'
        ),
        '#ebe1f5' => array(
            'color' => '#cfc2d9',
            'input_css_class' => 'c1b4cb',
            'hover_color' => '#faf0ff',
            'bg_color' => '#c1b4cb',
            'rgba' => '235,225,245,.2',
            'rgb_hover' => '251,242,255',
            'placeholder' => '#a699b0',
            'argb_hover_start' => '#02ebdef6',
            'argb_hover_finish' => '#ffebdef6',
            'argb_background_start' => '#02cfc2d9',
            'argb_background_finish' => '#ffcfc2d9'
        ),
        '#dedede' => array(
            'color' => '#c6c6c6',
            'input_css_class' => 'b9b9b9',
            'hover_color' => '#ededed',
            'bg_color' => '#b9b9b9',
            'rgba' => '222,222,222,.2',
            'rgb_hover' => '235,235,235',
            'placeholder' => '#9e9e9e',
            'argb_hover_start' => '#02e2e2e2',
            'argb_hover_finish' => '#ffe2e2e2',
            'argb_background_start' => '#02c6c6c6',
            'argb_background_finish' => '#ffc6c6c6'
        ),
        '#f2f2f2' => array(
            'color' => '#f2f2f2',
            'input_css_class' => 'ffffff',
            'hover_color' => '#fafafa',
            'bg_color' => '#bfbfbf',
            'rgba' => '242,242,242,.2',
            'rgb_hover' => '250,250,250',
            'placeholder' => '#9e9e9e',
            'argb_hover_start' => '#02fafafa',
            'argb_hover_finish' => '#fffafafa',
            'argb_background_start' => '#02e6e6e6',
            'argb_background_finish' => '#ffe6e6e6'
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

    public static function getDefaultColors()
    {
        return self::$types[MiniSiteConfig::DEFAULT_BACKGROUND_COLOR];
    }
}
