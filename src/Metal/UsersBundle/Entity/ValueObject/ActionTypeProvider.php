<?php

namespace Metal\UsersBundle\Entity\ValueObject;

class ActionTypeProvider
{
    //TODO: для пользователя резервируем константы с 21. После того, как перейдем на общее логирования этого не будет
    const CHANGE_EMAIL = 21;
    const ENABLED_STATUS = 22;
    const DISABLED_STATUS = 23;

    protected static $types = array(
        self::CHANGE_EMAIL => array(
            'id' => self::CHANGE_EMAIL,
            'title' => 'Смена электронного адреса',
        ),
        self::ENABLED_STATUS => array(
            'id' => self::ENABLED_STATUS,
            'title' => 'Включение пользователя',
        ),
        self::DISABLED_STATUS => array(
            'id' => self::DISABLED_STATUS,
            'title' => 'Отключение пользователя',
        )
    );

    public static function create($id)
    {
        return new ActionType(self::$types[$id]);
    }

    public static function getAllServices()
    {
        return array_map(
            function ($type) {
                return new ActionType($type);
            },
            self::$types
        );
    }

    public static function getAllServicesAsSimpleArray()
    {
        return array_map(
            function ($type) {
                return $type['title'];
            },
            self::$types
        );
    }

    public static function getAllServicesIds()
    {
        return array_keys(self::$types);
    }
}