<?php

namespace Metal\ProductsBundle\Entity\ValueObject;

class ProductMeasureProvider
{
    const WITHOUT_VOLUME = 0;

    protected static $types = array();
    protected static $typesObj = array();

    public static function initialize(array $types)
    {
        self::$types = $types;
        self::$typesObj = array_map(
            function ($type) {
                return new ProductMeasure($type);
            },
            self::$types
        );
    }

    public static function create($id)
    {
        return self::$typesObj[$id];
    }

    /**
     * @return ProductMeasure[]
     */
    public static function getAllTypes()
    {
        return self::$typesObj;
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

    public static function getAllTypesIds()
    {
        return array_keys(self::$types);
    }

    public static function createByPattern($subject)
    {
        foreach (self::$types as $id => $type) {
            foreach ($type['patterns'] as $pattern) {
                if (preg_match($pattern, $subject)) {
                    return self::$typesObj[$id];
                }
            }
        }

        return null;
    }
}
