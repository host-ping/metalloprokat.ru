<?php

namespace Metal\DemandsBundle\Entity\ValueObject;

class DemandPeriodicityProvider
{
    const ONCE = 1;
    const NONREGULAR = 2;
    const MONTHLY = 3;
    const QUARTERLY = 4;
    const WEEKLY = 5;
    const PERMANENT = 6;

    protected static $types = array(
        self::ONCE => array(
            'id' => self::ONCE,
            'title' => 'разовая',
        ),
        self::NONREGULAR => array(
            'id' => self::NONREGULAR,
            'title' => 'нерегулярная',
        ),
        self::MONTHLY => array(
            'id' => self::MONTHLY,
            'title' => 'ежемесячная',
        ),
        self::QUARTERLY => array(
            'id' => self::QUARTERLY,
            'title' => 'ежеквартальная',
        ),
        self::WEEKLY => array(
            'id' => self::WEEKLY,
            'title' => 'еженедельная',
        ),
        self::PERMANENT => array(
            'id' => self::PERMANENT,
            'title' => 'постоянная',
        ),
    );

    public static function create($id)
    {
        return new DemandPeriodicity(self::$types[$id]);
    }

    public static function getAllTypes()
    {
        return array_map(
            function ($type) {
                return new DemandPeriodicity($type);
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
