<?php

namespace Metal\ComplaintsBundle\Entity\ValueObject;

class ComplaintTypeProvider
{
    const SPAM_COMPLAINT = 5;

    protected static $types = array(
        1 => array(
            'id' => 1,
            'title' => 'Неправильная цена',
            'kind' => array('product'),
        ),
        2 => array(
            'id' => 2,
            'title' => 'Не отвечают по телефону',
            'kind' => array('company', 'demand', 'product'),
        ),
        3 => array(
            'id' => 3,
            'title' => 'Нет такой фирмы',
            'kind' => array('company', 'product'),
        ),
        4 => array(
            'id' => 4,
            'title' => 'Не умеют общаться',
            'kind' => array('company', 'demand', 'product'),
        ),
        self::SPAM_COMPLAINT => array(
            'id' => self::SPAM_COMPLAINT,
            'title' => 'Спам',
            'kind' => array('demand'),
        ),
    );

    public static function create($id)
    {
        return new ComplaintType(self::$types[$id]);
    }

    public static function getAllTypes()
    {
        return array_map(
            function ($type) {
                return new ComplaintType($type);
            },
            self::$types
        );
    }

    public static function getAllTypesAsSimpleArray($kind = null)
    {

        return array_filter(array_map(
            function ($type) use ($kind) {
                if ($kind && in_array($kind, $type['kind'])) {
                    return $type['title'];
                }
            },
            self::$types
        ));
    }

    public static function getAllTypesAsSimpleArrayWithoutKind()
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
