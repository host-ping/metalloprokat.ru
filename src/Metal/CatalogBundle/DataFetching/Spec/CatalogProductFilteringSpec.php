<?php

namespace Metal\CatalogBundle\DataFetching\Spec;

use Metal\AttributesBundle\DataFetching\Spec\AttributeSpec;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CatalogBundle\Entity\Brand;
use Metal\CategoriesBundle\DataFetching\Spec\CategorySpec;
use Metal\ProjectBundle\DataFetching\Spec\CacheableSpec;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\TerritorialBundle\DataFetching\Spec\TerritorialSpec;

use Symfony\Component\HttpFoundation\Request;

class CatalogProductFilteringSpec extends FilteringSpec implements CacheableSpec
{
    use TerritorialSpec;

    use CategorySpec;

    use AttributeSpec;

    public $loadProductsCountPerBrand = false;
    public $loadBrands = false;
    public $loadManufacturers = false;
    public $notBrand;
    public $match;

    /**
     * @param AttributeValue|Brand $brand
     *
     * @return $this
     */
    public function notBrand($brand)
    {
        $this->notBrand = $brand->getId();

        return $this;
    }

    public function loadProductsCountPerBrand($load)
    {
        $this->loadProductsCountPerBrand = $load;

        return $this;
    }

    public function loadBrands($load)
    {
        $this->loadBrands = $load;

        return $this;
    }

    public function loadManufacturers($load)
    {
        $this->loadManufacturers = $load;

        return $this;
    }

    public function match($match)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return static
     */
    public static function createFromRequest(Request $request)
    {
        $specification = (new static())
            ->category($request->attributes->get('category'))
            ->city($request->attributes->get('city'))
            ->region($request->attributes->get('region'))
            ->country($request->attributes->get('country'));

        if (count($attributesCollection = $request->attributes->get('attributes_collection'))) {
            $specification->attributesCollection($attributesCollection);
        } elseif ($attributes = $request->request->get('attribute')) {
            // айдишники из формы
            $specification->productAttrsByGroup($attributes);
        } elseif ($productParams = $request->attributes->get('products_parameters')) {
            //FIXME: delete-after-merge-facets
            // массивы из url
            $specification->productAttrs($productParams);
        }

        if ($q = $request->query->get('q')) {
            $specification->match($q);
        }

        return $specification;
    }

    public function getCacheKey()
    {
        if (null !== $this->productAttrsByGroup || null !== $this->notBrand || null !== $this->match) {
            return null;
        }

        $mode = 'products';
        if ($this->loadBrands) {
            $mode = 'brands';
        } elseif ($this->loadManufacturers) {
            $mode = 'manufacturers';
        }

        return sha1(serialize(array(
            'class' => __CLASS__,
            'mode' => $mode,
            'cityId' => $this->cityId,
            'regionId' => $this->regionId,
            'countryId' => $this->countryId,
            'categoryId' => $this->categoryId,
            'concreteCategoryId' => $this->concreteCategoryId,
        )));
    }

    public function resetTerritoryFilters()
    {
        $this->cityId = null;
        $this->regionId = null;
        $this->countryId = null;
    }
}
