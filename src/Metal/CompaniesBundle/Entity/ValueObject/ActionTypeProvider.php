<?php


namespace Metal\CompaniesBundle\Entity\ValueObject;


class ActionTypeProvider 
{
    //TODO: для компании резервируем константы до 21. После того, как перейдем на общее логирования этого не будет
    const CONNECT_USER = 1;
    const TOGGLE_STATUS_COMPANY = 2;
    const DISCONNECT_USER = 3;
    const UNION_COMPANY = 4;
    const CHANGE_COMPANY_TITLE = 5;
    const CHANGE_COMPANY_DATA = 6;
    const COMPANY_CREATION = 7;
    const CHANGE_CRM_STATUS = 8;
    const CHANGE_COMPANY_MAIN_CITY = 9;
    const EDIT_USER = 10;

    protected static $types = array(
        1 => array(
            'id' => 1,
            'title' => 'Присоединение пользователей',
        ),
        2 => array(
            'id' => 2,
            'title' => 'Включение/отключение компании',
        ),
        3 => array(
            'id' => 3,
            'title' => 'Отсоединение пользователей',
        ),
        4 => array(
            'id' => 4,
            'title' => 'Объединение компаний',
        ),
        5 => array(
            'id' => 5,
            'title' => 'Смена названия компании',
        ),
        6 => array(
            'id' => 6,
            'title' => 'Изменение контактных данных',
        ),
        7 => array(
            'id' => 7,
            'title' => 'Создание компании'
        ),
        8 => array(
            'id' => 8,
            'title' => 'Включение/Отключение CRM'
        ),
        9 => array(
            'id' => 9,
            'title' => 'Смена главного города'
        ),
        10 => array(
            'id' => 10,
            'title' => 'Редактирование сотрудника'
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
