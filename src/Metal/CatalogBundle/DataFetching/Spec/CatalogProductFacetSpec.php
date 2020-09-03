<?php

namespace Metal\CatalogBundle\DataFetching\Spec;

use Metal\AttributesBundle\Entity\Attribute;
use Metal\ProjectBundle\DataFetching\Spec\FacetSpec;

class CatalogProductFacetSpec extends FacetSpec
{
    const COLUMN_CATEGORIES_IDS = 'categories_ids';

    const COLUMN_CITIES_IDS = 'cities_ids';

    const COLUMN_CATEGORY_ID = 'category_id';

    const COLUMN_COUNTRIES_IDS = 'countries_ids';

    public function facetByAttribute(Attribute $attribute, CatalogProductFilteringSpec $criteria, $limit = null)
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

    public function facetByCities(CatalogProductFilteringSpec $criteria, $limit = null)
    {
        $facetCriteria = null;
        if ($criteria->cityId || $criteria->regionId) {
            $facetCriteria = clone $criteria;
            $facetCriteria->cityId(null);
            $facetCriteria->regionId(null);
        }

        return $this->facetBy(
            array(
                'column' => self::COLUMN_CITIES_IDS,
                'limit' => $limit,
                'criteria' => $facetCriteria,
            )
        );
    }

    public function facetByCategories(CatalogProductFilteringSpec $criteria, $limit = null)
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

    public function facetByCategory(CatalogProductFilteringSpec $criteria, $limit = null)
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

    public function facetByCountries(CatalogProductFilteringSpec $criteria, $limit = null)
    {
        $facetCriteria = null;
        if ($criteria->cityId || $criteria->regionId || $criteria->countryId) {
            $facetCriteria = clone $criteria;
            $facetCriteria->cityId(null);
            $facetCriteria->regionId(null);
            $facetCriteria->countryId(null);
        }

        return $this->facetBy(
            array(
                'column' => self::COLUMN_COUNTRIES_IDS,
                'limit' => $limit,
                'criteria' => $facetCriteria,
            )
        );
    }
}
