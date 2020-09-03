<?php

namespace Metal\ServicesBundle\Entity\ValueObject;

class ServicePeriodTypesProvider
{
    const QUARTER = 1;
    const HALF_YEAR = 2;
    const YEAR = 3;
    const MONTH = 4;

    protected static $types = array(
        self::MONTH => array(
            'id' => self::MONTH,
            'title' => 'на месяц',
        ),
        self::QUARTER => array(
            'id' => self::QUARTER,
            'title' => 'на квартал',
        ),
        self::HALF_YEAR => array(
            'id' => self::HALF_YEAR,
            'title' => 'на полгода',
        ),
        self::YEAR => array(
            'id' => self::YEAR,
            'title' => 'на год',
        ),
    );

    public static function create($id)
    {
        return new ServicePeriodType(self::$types[$id]);
    }

    /**
     * @return ServicePeriodType[]
     */
    public static function getAllTypes()
    {
        return array_map(
            function ($type) {
                return new ServicePeriodType($type);
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

    public static function getAllPeriodsNameByIds()
    {
        return array(
            self::MONTH => 'Месяц',
            self::QUARTER => 'Квартал',
            self::HALF_YEAR => 'Полгода',
            self::YEAR => 'Год',
        );
    }

    public static function getAllTypesIds()
    {
        return array_keys(self::$types);
    }
}
