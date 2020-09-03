<?php

namespace Metal\AnnouncementsBundle\Entity\ValueObject;

class SectionProvider
{
    const TYPE_EMAIL = 4;

    protected static $types = array(
        1 => array(
            'id' => 1,
            'title' => 'Сквозное',
            'longTitle' => 'Сквозное размещение по порталу (Россия + СНГ, Титульная + внутренние страницы)',
        ),
        2 => array(
            'id' => 2,
            'title' => 'Поставщики',
            'longTitle' => 'Раздел Поставщики',
        ),
        3 => array(
            'id' => 3,
            'title' => 'Потребители',
            'longTitle' => 'Раздел Потребители',
        ),
        4 => array(
            'id' => 4,
            'title' => 'Email-рассылки',
            'longTitle' => 'Email-рассылки',
        )
    );

    public static function create($id)
    {
        return new Section(self::$types[$id]);
    }

    public static function getAllTypes()
    {
        return array_map(
            function ($type) {
                return new Section($type);
            },
            self::$types
        );
    }

    public static function getAllTypesAsSimpleArray()
    {
        return array_map(
            function ($type) {
                return $type['title'];
            },
            self::$types
        );
    }

    public static function getAllTypesIds()
    {
        return array_keys(self::$types);
    }
}
