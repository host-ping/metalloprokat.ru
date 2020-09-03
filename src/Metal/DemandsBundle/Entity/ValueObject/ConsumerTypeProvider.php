<?php

namespace Metal\DemandsBundle\Entity\ValueObject;

class ConsumerTypeProvider
{
    const TRADER = 1;
    const CONSUMER = 2;
    const VERIFY = 3;
    const UNKNOWN = 4;

    protected static $types = array();

    public static function initialize($dictionary)
    {
        self::$types = array(
            self::TRADER => array(
                'id' => self::TRADER,
                'title' => $dictionary['trader'],
            ),
            self::CONSUMER => array(
                'id' => self::CONSUMER,
                'title' => 'конечный потребитель',
            ),
            self::VERIFY => array(
                'id' => self::VERIFY,
                'title' => 'проверяется',
            ),
            self::UNKNOWN => array(
                'id' => self::UNKNOWN,
                'title' => 'не указано',
            ),
        );
    }

    public static function create($id)
    {
        return new ConsumerType(self::$types[$id]);
    }

    public static function getAllTypes()
    {
        return array_map(
            function ($type) {
                return new ConsumerType($type);
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
