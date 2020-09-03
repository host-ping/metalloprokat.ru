<?php

namespace Metal\ContentBundle\Entity\ValueObject;

class StatusTypeProvider
{
    const USER_DRAFT = 1;
    const NOT_CHECKED = 2;
    const CHECKED = 3;
    const REJECTED = 4;
    const SPAM = 5;
    const POTENTIAL_SPAM = 6;

    protected static $types = array(
        self::USER_DRAFT => array(
            'id' => self::USER_DRAFT,
            'title' => 'Черновик пользователя',
        ),
        self::NOT_CHECKED => array(
            'id' => self::NOT_CHECKED,
            'title' => 'Не проверено',
        ),
        self::CHECKED => array(
            'id' => self::CHECKED,
            'title' => 'Проверено',
        ),
        self::REJECTED => array(
            'id' => self::REJECTED,
            'title' => 'Отклонено',
        ),
        self::SPAM => array(
            'id' => self::SPAM,
            'title' => 'Спам',
        ),
        self::POTENTIAL_SPAM => array(
            'id' => self::POTENTIAL_SPAM,
            'title' => 'Потенциальный спам',
        ),
    );


    public static function create($id)
    {
        return new StatusType(self::$types[$id]);
    }

    public static function getAllTypes()
    {
        return array_map(
            function ($type) {
                return new StatusType($type);
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
