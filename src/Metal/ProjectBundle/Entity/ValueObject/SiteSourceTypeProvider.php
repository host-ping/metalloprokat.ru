<?php

namespace Metal\ProjectBundle\Entity\ValueObject;

class SiteSourceTypeProvider
{
    const SOURCE_PORTAL = 1;
    const SOURCE_FROM_CALLBACK = 6;
    const SOURCE_COPY_OF_PRIVATE = 7;
    const SOURCE_MIRROR_FROM_METALLOPROKAT = 9;
    const SOURCE_ADMIN = 4;

    const SOURCE_SPROS = 2;
    const SOURCE_8_800 = 3;
    const IMPORT_FROM_TORGMET = 5;

    const SOURCE_PRODUCT = 8;

    protected static $types = array(
        1 => array(
            'id' => 1,
            'title' => 'Портал',
            'pattern' => null,
        ),
        4 => array(
            'id' => 4,
            'title' => 'Админка',
            'pattern' => null,
        ),
        6 => array(
            'id' => 6,
            'title' => 'Портал (обратные звонки)',
            'pattern' => null,
        ),
        7 => array(
            'id' => 7,
            'title' => 'Портал (копия приватной)',
            'pattern' => null,
        ),
        self::SOURCE_MIRROR_FROM_METALLOPROKAT => array(
            'id' => self::SOURCE_MIRROR_FROM_METALLOPROKAT,
            'title' => 'Копия заявки (с металлопроката)',
            'pattern' => null,
        ),
    );

    public static function initializeAdditionalSiteType(array $additionalType = array())
    {
        self::$types = array_replace(self::$types, $additionalType);
    }

    public static function create($id)
    {
        return new SiteSourceType(self::$types[$id]);
    }

    public static function getAllTypes()
    {
        return array_map(function($type) {
                return new SiteSourceType($type);
            }, self::$types);
    }

    public static function getAllTypesAsSimpleArray()
    {
        return array_map(function($type) {
                return $type['title'];
            }, self::$types);
    }

    public static function getAllTypesAsSimpleArrayWithExclude($excludeId)
    {
        return array_map(function($type) {
            return $type['title'];
        }, array_diff_key(self::$types, array_flip(array($excludeId))));
    }

    public static function getAllTypesIds()
    {
        return array_keys(self::$types);
    }

    public static function getSourceByHost($host)
    {
        foreach (self::$types as $type) {
            if ($type['pattern'] && preg_match($type['pattern'], $host)) {
                return new SiteSourceType($type);
            }
        }

        return new SiteSourceType(self::$types[SiteSourceTypeProvider::SOURCE_PORTAL]);
    }
}
