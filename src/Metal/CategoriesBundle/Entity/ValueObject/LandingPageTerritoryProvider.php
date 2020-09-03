<?php

namespace Metal\CategoriesBundle\Entity\ValueObject;

class LandingPageTerritoryProvider
{
    const ALL_COUNTRIES = 1;
    const ALL_COUNTRIES_AND_CITIES = 2;
    const SELECTED_TERRITORY = 3;

    protected static $types = array(
        self::ALL_COUNTRIES => array(
            'id' => self::ALL_COUNTRIES,
            'title' => 'По странам',
        ),
        self::ALL_COUNTRIES_AND_CITIES => array(
            'id' => self::ALL_COUNTRIES_AND_CITIES,
            'title' => 'Везде',
        ),
        self::SELECTED_TERRITORY => array(
            'id' => self::SELECTED_TERRITORY,
            'title' => 'Заданная территория',
        ),
    );

    public static function create($id)
    {
        return new LandingPageTerritory(self::$types[$id]);
    }

    public static function getAllTypes()
    {
        return array_map(
            function ($type) {
                return new LandingPageTerritory($type);
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
