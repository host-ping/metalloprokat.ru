<?php

namespace Metal\ProductsBundle\Entity\ValueObject;

class CurrencyProvider
{
    protected static $types = array(
        1 => array(
            'id' => 1,
            'token' => '₽',
            'tokenEn' => 'RUB',
            'symbolClass' => 'icon-rouble',
            'fallbackToken' => 'руб.'
        ),
        2 => array(
            'id' => 2,
            'token' => '$',
            'tokenEn' => 'USD',
            'symbolClass' => '',
            'fallbackToken' => '$'
        ),
        3 => array(
            'id' => 3,
            'token' => '€',
            'tokenEn' => 'EUR',
            'symbolClass' => 'icon-euro',
            'fallbackToken' => 'euro'
        ),
        4 => array(
            'id' => 4,
            'token' => '₴',
            'tokenEn' => 'UAH',
            'symbolClass' => 'icon-hrivna',
            'fallbackToken' => 'грн.'
        ),
        5 => array(
            'id' => 5,
            'token' => '₸',
            'tokenEn' => 'KZT',
            'symbolClass' => 'icon-tenge',
            'fallbackToken' => 'тенге'
        ),
        6 => array(
            'id' => 6,
            'token' => 'Br',
            'tokenEn' => 'BYR',
            'symbolClass' => '',
            'fallbackToken' => 'Br'
        ),
    );

    public static function create($id)
    {
        if (!isset(self::$types[$id])) {
            return null;
        }

        return new Currency(self::$types[$id]);
    }

    /**
     * @return Currency[]
     */
    public static function getAllTypes()
    {
        return array_map(
            function ($type) {
                return new Currency($type);
            },
            self::$types
        );
    }

    public static function getAllTypesAsSimpleArray()
    {
        return array_map(
            function ($type) {
                return $type['token'];
            },
            self::$types
        );
    }

    public static function getTypeByTokenEn($tokenEn)
    {
        foreach (self::$types as $type) {
            if (mb_strtolower($tokenEn) === mb_strtolower($type['tokenEn'])) {
                return new Currency($type);
            }
        }
    }

    public static function getAllTypesEnAsSimpleArray()
    {
        return array_map(
            function ($type) {
                return $type['tokenEn'];
            },
            self::$types
        );
    }

    public static function getAllTypesIds()
    {
        return array_keys(self::$types);
    }
}
