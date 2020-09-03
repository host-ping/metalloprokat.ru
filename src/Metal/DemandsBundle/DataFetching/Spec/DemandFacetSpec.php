<?php

namespace Metal\DemandsBundle\DataFetching\Spec;

use Metal\AttributesBundle\Entity\Attribute;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProjectBundle\DataFetching\Spec\FacetSpec;

class DemandFacetSpec extends FacetSpec
{
    const COLUMN_COUNTRIES_IDS = 'country_id';

    const COLUMN_CATEGORIES_IDS = 'categories_ids';

    const COLUMN_CATEGORY_ID = 'category_id';

    const COLUMN_CREATED_AT_INTERVAL = 'created_at_interval';

    const COLUMN_CITY_ID = 'city_id';

    const COLUMN_ATTRIBUTES_IDS = 'attributes_ids';

    public function facetByCities(DemandFilteringSpec $criteria, $limit = null)
    {
        $facetCriteria = null;
        if ($criteria->cityId || $criteria->regionId || $criteria->countryId) {
            $facetCriteria = clone $criteria;
            $facetCriteria->cityId(null);
            $facetCriteria->regionId(null);
        }

        return $this->facetBy(
            array(
                'column' => self::COLUMN_CITY_ID,
                'limit' => $limit,
                'criteria' => $facetCriteria,
            )
        );
    }

    public function facetByCategories(DemandFilteringSpec $criteria, $limit = null)
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

    public function facetByAttribute(Attribute $attribute, Category $category, DemandFilteringSpec $criteria, $limit = null)
    {
        $facetCriteria = null;
        $attributeId = $attribute->getId();
        if (isset($criteria->productAttrsByGroup[$attributeId])) {
            $facetCriteria = clone $criteria;
            unset($facetCriteria->productAttrsByGroup[$attributeId]);
        }

        return $this->facetBy(
            array(
                'column' => sprintf('attributes.%d.%d', $category->getRealCategoryId(), $attributeId),
                'limit' => $limit,
                'criteria' => $facetCriteria,
            )
        );
    }

    public function facetByCreatedAt(DemandFilteringSpec $criteria, $limit = null)
    {
        $column = array(
            sprintf(
                'interval(created_at, %d, %d, %d, %d)',
                strtotime('-1 year'),
                strtotime('-1 month'),
                strtotime('-1 week'),
                strtotime('-1 day')
            ) => DemandFacetSpec::COLUMN_CREATED_AT_INTERVAL,
        );

        $facetCriteria = clone $criteria;
        $facetCriteria->dateTo(null);
        $facetCriteria->dateFrom(null);

        return $this->facetBy(
            array(
                'column' => $column,
                'name' => self::COLUMN_CREATED_AT_INTERVAL,
                'limit' => $limit,
                'criteria' => $facetCriteria,
            )
        );
    }

    public function facetByCategory(DemandFilteringSpec $criteria, $limit = null)
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
}
