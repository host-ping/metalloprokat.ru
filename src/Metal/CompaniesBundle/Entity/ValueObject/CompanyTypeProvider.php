<?php

namespace Metal\CompaniesBundle\Entity\ValueObject;

class CompanyTypeProvider
{
    protected static $types = array(
        1 => array(
            'id' => 1,
            'title' => 'ООО',
        ),
        2 => array(
            'id' => 2,
            'title' => 'ЗАО',
        ),
        3 => array(
            'id' => 3,
            'title' => 'ОАО',
        ),
        4 => array(
            'id' => 4,
            'title' => 'ИП',
        ),
        5 => array(
            'id' => 5,
            'title' => 'ТОО',
        ),
        7 => array(
            'id' => 7,
            'title' => 'ФГУП',
        ),
        8 => array(
            'id' => 8,
            'title' => 'АО',
        ),
        9 => array(
            'id' => 9,
            'title' => 'ЧП',
        ),
        10 => array(
            'id' => 10,
            'title' => 'ПАО',
        ),
        6 => array(
            'id' => 6,
            'title' => '-',
            'hidden' => true,
        ),
    );

    /**
     * @return CompanyType
     */
    public static function create($id)
    {
        return new CompanyType(self::$types[$id]);
    }

    /**
     * @return CompanyType[]
     */
    public static function getAllTypes()
    {
        return array_map(
            function ($type) {
                return new CompanyType($type);
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
            array_filter(
                self::$types,
                function ($type) {
                    return empty($type['hidden']);
                }
            )
        );
    }

    public static function getAllTypesIds()
    {
        return array_keys(self::$types);
    }
}
