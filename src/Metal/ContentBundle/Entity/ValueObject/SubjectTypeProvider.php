<?php

namespace Metal\ContentBundle\Entity\ValueObject;

class SubjectTypeProvider
{
    const PROJECTS_AND_TECHNOLOGIES = 1;
    const DECORATION_MATERIALS = 2;
    const WORKS = 3;
    const FURNITURE = 4;

    protected static $types = array(
        self::PROJECTS_AND_TECHNOLOGIES => array(
            'id' => self::PROJECTS_AND_TECHNOLOGIES,
            'title' => 'Проекты и технологии',
        ),
        self::DECORATION_MATERIALS => array(
            'id' => self::DECORATION_MATERIALS,
            'title' => 'Отделочные материалы',
        ),
        self::WORKS => array(
            'id' => self::WORKS,
            'title' => 'Выполнение работ',
        ),
        self::FURNITURE => array(
            'id' => self::FURNITURE,
            'title' => 'Мебель, декор, аксессуары',
        ),
    );

    public static function create($id)
    {
        if (!isset(self::$types[$id])) {
            return;
        }

        return new StatusType(self::$types[$id]);
    }

    public static function getAllTypes()
    {
        return array_map(
            function ($type) {
                return new StatusType($type);
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