<?php

namespace Metal\ProductsBundle\DataFetching\Spec;

use Metal\AttributesBundle\Entity\Attribute;
use Metal\ProjectBundle\DataFetching\Spec\FacetSpec;

class ProductsFacetSpec extends FacetSpec
{
    public const COLUMN_PRODUCT_CITY_ID = 'product_city_id';

    public const COLUMN_COMPANY_CITY_ID = 'company_city_id';

    public const COLUMN_COUNTRIES_IDS = 'countries_ids';

    public const COLUMN_CATEGORY_ID = 'category_id';

    public const COLUMN_CATEGORIES_IDS = 'categories_ids';

    public const COLUMN_CUSTOM_CATEGORIES_IDS = 'custom_categories_ids';

    public const COLUMN_ATTRIBUTES_IDS = 'attributes_ids';

    public const COLUMN_CITIES_IDS = 'cities_ids';

    public function facetByAttribute(Attribute $attribute, ProductsFilteringSpec $criteria, $limit = null)
    {
        $facetCriteria = null;
        $attributeId = $attribute->getId();
        if (isset($criteria->productAttrsByGroup[$attributeId])) {
            $facetCriteria = clone $criteria;
            unset($facetCriteria->productAttrsByGroup[$attributeId]);
        }

        return $this->facetBy(
            array(
                'column' => sprintf('attributes.%d', $attributeId),
                'limit' => $limit,
                'criteria' => $facetCriteria,
            )
        );
    }

    public function facetByProductCity(ProductsFilteringSpec $criteria, $limit = null)
    {
        $facetCriteria = null;
        if ($criteria->cityId || $criteria->regionId || $criteria->companyCityId || $criteria->countryId || $criteria->productCityId) {
            $facetCriteria = clone $criteria;
            // в данном случае страна должна обнуляться, потому что метод вызывается с минисайта и сайтмапа
            $facetCriteria->resetTerritoryFilters(true);
        }

        return $this->facetBy(
            array(
                'column' => self::COLUMN_PRODUCT_CITY_ID,
                'limit' => $limit,
                'criteria' => $facetCriteria,
            )
        );
    }

    public function facetByCompanyCity(ProductsFilteringSpec $criteria, $limit = null)
    {
        $facetCriteria = null;
        if ($criteria->cityId || $criteria->regionId || $criteria->companyCityId || $criteria->countryId || $criteria->productCityId) {
            $facetCriteria = clone $criteria;
            $facetCriteria->resetTerritoryFilters();
        }

        return $this->facetBy(
            array(
                'column' => self::COLUMN_COMPANY_CITY_ID,
                'limit' => $limit,
                'criteria' => $facetCriteria,
            )
        );
    }

    public function facetByCountries(ProductsFilteringSpec $criteria, $limit = null)
    {
        $facetCriteria = null;
        if ($criteria->hasTerritoryFilters(true)) {
            $facetCriteria = clone $criteria;
            $facetCriteria->resetTerritoryFilters(true);
        }

        return $this->facetBy(
            array(
                'column' => self::COLUMN_COUNTRIES_IDS,
                'limit' => $limit,
                'criteria' => $facetCriteria,
            )
        );
    }

    public function facetByCities(ProductsFilteringSpec $criteria, $limit = null)
    {
        $facetCriteria = null;
        if ($criteria->hasTerritoryFilters()) {
            $facetCriteria = clone $criteria;
            $facetCriteria->resetTerritoryFilters();
        }

        return $this->facetBy(
            array(
                'column' => self::COLUMN_CITIES_IDS,
                'limit' => $limit,
                'criteria' => $facetCriteria,
            )
        );
    }

    public function facetByCategories(ProductsFilteringSpec $criteria, $limit = null)
    {
        $facetCriteria = null;
        if ($criteria->categoryId || $criteria->concreteCategoryId) {
            $facetCriteria = clone $criteria;
            $facetCriteria->categoryId(null);
            $facetCriteria->concreteCategoryId(null);
        }

        return $this->facetBy(
            array(
                'column' => self::COLUMN_CATEGORIES_IDS,
                'limit' => $limit,
                'criteria' => $facetCriteria,
            )
        );
    }

    public function facetByCustomCategories(ProductsFilteringSpec $criteria, $limit = null)
    {
        return $this->facetBy(
            array(
                'column' => self::COLUMN_CUSTOM_CATEGORIES_IDS,
                'limit' => $limit,
                'criteria' => $criteria,
            )
        );
    }

    public function facetByAttributes($limit = 10000)
    {
        return $this->facetBy(
            array(
                'column' => self::COLUMN_ATTRIBUTES_IDS,
                'limit' => $limit,
                'criteria' => null,
            )
        );
    }

    public function facetByAttributesForEmptyAttributes(ProductsFilteringSpec $criteria, $limit = null)
    {
        $facetCriteria = clone $criteria;
        $facetCriteria->productAttrsByGroup(null);

        return $this->facetBy(
            array(
                'column' => self::COLUMN_ATTRIBUTES_IDS,
                'limit' => $limit,
                'criteria' => $facetCriteria,
            )
        );
    }

    public function facetByCategory(ProductsFilteringSpec $criteria, $limit = null)
    {
        $facetCriteria = null;
        if ($criteria->categoryId || $criteria->concreteCategoryId) {
            $facetCriteria = clone $criteria;
            $facetCriteria->categoryId(null);
            $facetCriteria->concreteCategoryId(null);
        }

        return $this->facetBy(
            array(
                'column' => self::COLUMN_CATEGORY_ID,
                'limit' => $limit,
                'criteria' => $facetCriteria,
            )
        );
    }

    public function facetByConcreteCategory($limit = null)
    {
        return $this->facetBy(
            array(
                'column' => self::COLUMN_CATEGORY_ID,
                'limit' => $limit
            )
        );
    }
}
