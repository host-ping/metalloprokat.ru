<?php

namespace Metal\CompaniesBundle\Entity\ValueObject;

use Metal\ServicesBundle\Entity\Package;

class CompanyPackageTypeProvider
{
    protected static $types = array(
        Package::BASE_PACKAGE => array(
            'id' => Package::BASE_PACKAGE,
            'title' => 'Базовый',
            'titleGenitive' => 'Базового',
        ),
        Package::ADVANCED_PACKAGE => array(
            'id' => Package::ADVANCED_PACKAGE,
            'title' => 'Расширенный',
            'titleGenitive' => 'Расширенного',
        ),
        Package::FULL_PACKAGE => array(
            'id' => Package::FULL_PACKAGE,
            'title' => 'Полный',
            'titleGenitive' => 'Полного',
        ),
        Package::STANDARD_PACKAGE => array(
            'id' => Package::STANDARD_PACKAGE,
            'title' => 'Стартовый',
            'titleGenitive' => 'Стартового',
        ),
    );

    public static function create($id)
    {
        return new CompanyPackageType(self::$types[$id]);
    }

    public static function getPayedPackagesAsSimpleArray()
    {
        return array(
            Package::STANDARD_PACKAGE => 'Стартовый пакет',
            Package::ADVANCED_PACKAGE => 'Расширенный пакет',
            Package::FULL_PACKAGE => 'Полный пакет',
        );
    }

    public static function getAllCompanyPackagesAsSimpleArray()
    {
        return array_map(
            function ($type) {
                return $type['title'];
            },
            self::$types
        );
    }
}
