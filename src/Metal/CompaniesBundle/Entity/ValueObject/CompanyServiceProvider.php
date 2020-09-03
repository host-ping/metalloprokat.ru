<?php

namespace Metal\CompaniesBundle\Entity\ValueObject;

class CompanyServiceProvider
{
    protected static $types = array();

    public static function initializeCompanyServiceType(array $additionalType = array())
    {
        self::$types = $additionalType;
    }

    public static function create($id)
    {
        return new CompanyService(self::$types[$id]);
    }

    public static function getAllServices()
    {
        return array_map(
            function ($type) {
                return new CompanyService($type);
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