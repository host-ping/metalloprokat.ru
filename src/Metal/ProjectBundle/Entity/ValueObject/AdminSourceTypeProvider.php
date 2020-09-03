<?php

namespace Metal\ProjectBundle\Entity\ValueObject;

class AdminSourceTypeProvider
{
    const ADMIN_SOURCE_ADVERT = 3;
    const ADMIN_SOURCE_PHONE = 4;
    const ADMIN_SOURCE_GRABBER = 6;

    protected static $types = array(
        1 => array(
            'id' => 1,
            'title' => 'Модерация',
        ),
        2 => array(
            'id' => 2,
            'title' => 'Переписка',
        ),
        3 => array(
            'id' => 3,
            'title' => 'Объявление',
        ),
        4 => array(
            'id' => 4,
            'title' => 'Телефон',
        ),
        5 => array(
            'id' => 5,
            'title' => 'Чат на сайте',
        ),
        6 => array(
            'id' => 6,
            'title' => 'Граббер',
        ),
        7 => array(
            'id' => 7,
            'title' => 'Кнопка внизу',
        ),
        8 => array(
            'id' => 8,
            'title' => 'С минисайта',
        ),
    );

    public static function create($id)
    {
        return new AdminSourceType(self::$types[$id]);
    }

    public static function getAllTypes()
    {
        return array_map(function($type) {
            return new AdminSourceType($type);
        }, self::$types);
    }

    public static function getAllTypesAsSimpleArray()
    {
        return array_map(function($type) {
            return $type['title'];
        }, self::$types);
    }

    public static function getAllTypesIds()
    {
        return array_keys(self::$types);
    }
}
