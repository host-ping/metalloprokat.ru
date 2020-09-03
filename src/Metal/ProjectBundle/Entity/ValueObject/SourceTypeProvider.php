<?php

namespace Metal\ProjectBundle\Entity\ValueObject;

class SourceTypeProvider
{
    const MINISITE = 3;
    const PRODUCTS = 5;
    const OTHER = 4;
    const PRODUCT_VIEW = 2;
    const ALL_COMPANY_PRODUCTS = 6;
    const EMAIL_DEMANDS_TYPE_ID = 13;

    protected static $types = array(
        1 => array(
            'id' => 1,
            'title' => 'Со списка компаний',
            'slug' => 'companies-list'
        ),
        self::PRODUCT_VIEW => array(
            'id' => self::PRODUCT_VIEW,
            'title' => 'С товара',
            'slug' => 'product'
        ),
        self::MINISITE => array(
            'id' => self::MINISITE,
            'title' => 'С мини-сайта',
            'slug' => 'mini-site'
        ),
        self::OTHER => array(
            'id' => self::OTHER,
            'title' => 'Другое',
            'slug' => 'other'
        ),
        self::PRODUCTS => array(
            'id' => self::PRODUCTS,
            'title' => 'Со списка товаров',
            'slug' => 'products-list'
        ),
        self::ALL_COMPANY_PRODUCTS => array(
            'id' => self::ALL_COMPANY_PRODUCTS,
            'title' => 'Со всех товаров компании',
            'slug' => 'all-company-products'
        ),
        7 => array(
            'id' => 7,
            'title' => 'Со списка избранных компаний',
            'slug' => 'favorites-companies'
        ),
        8 => array(
            'id' => 8,
            'title' => 'С контактов мини-сайта',
            'slug' => 'mini-site-contacts'
        ),
        9 => array(
            'id' => 9,
            'title' => 'Со списка заявок',
            'slug' => 'demands-list'
        ),
        10 => array(
            'id' => 10,
            'title' => 'С просмотра заявки',
            'slug' => 'demand-view'
        ),
        11 => array(
            'id' => 11,
            'title' => 'С главной страницы потребителей',
            'slug' => 'frontpage-demands'
        ),
        12 => array(
            'id' => 12,
            'title' => 'С главной страницы',
            'slug' => 'frontpage'
        ),
        self::EMAIL_DEMANDS_TYPE_ID => array(
            'id' => self::EMAIL_DEMANDS_TYPE_ID,
            'title' => 'С почты заявок',
            'slug' => 'email-demands'
        ),
        14 => array(
            'id' => 14,
            'title' => 'Со страницы контента',
            'slug' => 'content'
        ),
        15 => array(
            'id' => 15,
            'title' => 'Со списка продуктов',
            'slug' => 'catalog-products'
        ),
        16 => array(
            'id' => 16,
            'title' => 'С продукта',
            'slug' => 'catalog-product'
        ),
        17 => array(
            'id' => 17,
            'title' => 'С производителя',
            'slug' => 'catalog-manufacturer'
        ),
        18 => array(
            'id' => 18,
            'title' => 'С бренда',
            'slug' => 'catalog-brand'
        ),
        19 => array(
            'id' => 19,
            'title' => 'Со списка производителей',
            'slug' => 'catalog-manufacturers'
        ),
        20 => array(
            'id' => 20,
            'title' => 'Со списка брендов',
            'slug' => 'catalog-brands'
        ),
        21 => array(
            'id' => 21,
            'title' => 'С продуктов мини-сайта',
            'slug' => 'mini-site-products'
        ),
    );

    public static function create($id)
    {
        return new SourceType(self::$types[$id]);
    }

    public static function createBySlug($slug)
    {
        foreach (self::$types as $type) {
            if ($slug == $type['slug']) {
                return new SourceType($type);
            }
        }

        throw new \InvalidArgumentException();
    }

    public static function getAllTypes()
    {
        return array_map(
            function ($type) {
                return new SourceType($type);
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
