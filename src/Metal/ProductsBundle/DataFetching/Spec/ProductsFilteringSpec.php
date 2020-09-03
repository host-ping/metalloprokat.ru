<?php

namespace Metal\ProductsBundle\DataFetching\Spec;

use Metal\AttributesBundle\DataFetching\Spec\AttributeSpec;
use Metal\CategoriesBundle\DataFetching\Spec\CategorySpec;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\DataFetching\Spec\ComparableSpec;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\TaggableCacheableSpec;
use Metal\TerritorialBundle\DataFetching\Spec\TerritorialSpec;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\TerritoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductsFilteringSpec extends FilteringSpec implements TaggableCacheableSpec, ComparableSpec
{
    public const COMPANY_TAG_PATTERN = 'COMPANY_%d';

    public $matchTitle;

    public $companyId;

    public $exceptCompaniesIds = [];

    public $companyCityId;

    public $productCityId;

    public $priorityShow;

    use TerritorialSpec;

    use CategorySpec;

    use AttributeSpec;

    /**
     * @var array with keys "min", "max"
     */
    public $price;

    public $exceptProductsIds = [];

    public $companyAttrs = [];

    public $allowVirtual = false;

    public $loadCompanies = false;

    public $loadPriceRange = false;

    public $loadProductsCountPerCompany = false;

    public $loadSpecialOfferProduct = false;

    public $specialOffer;

    public $isHotOffer;

    public $loadRandomSpecialOfferProductPerCompany = false;

    public $showOnPortal = true;

    public $notEmptyAttributes;

    public $cacheTags = [];

    public function specialOffer($specialOffer)
    {
        $this->specialOffer = $specialOffer;

        return $this;
    }

    public function isHotOffer($isHotOffer)
    {
        $this->isHotOffer = $isHotOffer;

        return $this;
    }

    public function notEmptyAttributes($notEmptyAttributes = true)
    {
        $this->notEmptyAttributes = $notEmptyAttributes;

        return $this;
    }

    public function showOnPortal($showOnPortal)
    {
        $this->showOnPortal = $showOnPortal;

        return $this;
    }

    public function companyCityId($companyCityId)
    {
        $this->companyCityId = $companyCityId;

        return $this;
    }

    public function companyCity(City $city = null)
    {
        if ($city) {
            $this->companyCityId($city->getId());
            $this->country($city->getCountry());
        }

        return $this;
    }

    public function productCityId($productCityId)
    {
        $this->productCityId = $productCityId;

        return $this;
    }

    public function productCity(City $city = null)
    {
        if ($city) {
            $this->productCityId($city->getId());
            $this->country($city->getCountry());
        }

        return $this;
    }

    public function matchTitle($titleToMatch)
    {
        $this->matchTitle = $titleToMatch;

        return $this;
    }

    public function companyId($companyId)
    {
        $this->companyId = $companyId;
        $this->cacheTags[] = sprintf(self::COMPANY_TAG_PATTERN, $companyId);

        return $this;
    }

    public function company(Company $company)
    {
        return $this->companyId($company->getId());
    }

    public function loadRandomSpecialOfferProductPerCompany($load = true)
    {
        $this->loadRandomSpecialOfferProductPerCompany = $load;
    }

    public function exceptCompany(Company $company)
    {
        $this->exceptCompaniesIds[] = $company->getId();

        return $this;
    }

    public function price($min, $max)
    {
        $this->price = array_filter(array('min' => $min, 'max' => $max));
        if (!$this->price) {
            $this->price = null;
        }

        return $this;
    }

    public function clearPrice()
    {
        $this->price = null;

        return $this;
    }

    public function onlyPriorityShowCompanies(TerritoryInterface $territory = null)
    {
        $this->priorityShow = $territory ? [$territory->getKind() => $territory->getId()] : true;

        return $this;
    }

    public function exceptProductId($id)
    {
        $this->exceptProductsIds[] = $id;

        return $this;
    }

    /**
     * @param iterable|Product[] $products
     *
     * @return $this
     */
    public function exceptProducts(iterable $products)
    {
        foreach ($products as $product) {
            $this->exceptProductsIds[] = $product->getId();
        }

        return $this;
    }

    /**
     * @param iterable|Company[] $companies
     *
     * @return $this
     */
    public function exceptCompanies(iterable $companies)
    {
        foreach ($companies as $company) {
            $this->exceptCompaniesIds[] = $company->getId();
        }

        return $this;
    }

    public function companyAttrs($attrs)
    {
        $this->companyAttrs = (array)$attrs;

        return $this;
    }

    public function allowVirtual($allow)
    {
        $this->allowVirtual = $allow;

        return $this;
    }

    public function loadCompanies($load)
    {
        $this->loadCompanies = $load;

        return $this;
    }

    public function loadPriceRange($load)
    {
        $this->loadPriceRange = $load;

        return $this;
    }

    public function loadProductsCountPerCompany($load)
    {
        $this->loadProductsCountPerCompany = $load;

        return $this;
    }

    public function loadSpecialOfferProduct($load)
    {
        $this->loadSpecialOfferProduct = $load;

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
            ->price($request->query->get('price_from'), $request->query->get('price_to'))
            ->matchTitle($request->query->get('q'))
            ->companyAttrs($request->query->get('cattr'));

        $specification
            ->city($request->attributes->get('city'))
            ->region($request->attributes->get('region'))
            ->country($request->attributes->get('country'));

        if ($request->query->get('concrete_city')) {
            $specification->companyCity($request->attributes->get('city'));
        }

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

        return $specification;
    }

    public function getCacheKey()
    {
        if (null !== $this->matchTitle || array() !== $this->exceptCompaniesIds || null !== $this->price
            || array() !== $this->exceptProductsIds || array() !== $this->companyAttrs
            || $this->loadRandomSpecialOfferProductPerCompany) {
            return null;
        }

        if (null !== $this->productAttrsByGroup) {
            if (count($this->productAttrsByGroup) > 1) {
                // выбрали больше 1 атрибута
                return null;
            }

            foreach ($this->productAttrsByGroup as $attributes) {
                if (count($attributes) > 1) {
                    // выбрали больше 1 значения атрибута
                    return null;
                }
            }
        }

        $cacheParts = array(
            'class' => __CLASS__,
            'mode' => $this->loadCompanies ? 'companies' : 'products',
            'companyCityId' => $this->companyCityId,
            'productCityId' => $this->productCityId,
            'cityId' => $this->cityId,
            'regionId' => $this->regionId,
            'countryId' => $this->countryId,
            'categoryId' => $this->categoryId,
            'concreteCategoryId' => $this->concreteCategoryId,
            'allowVirtual' => $this->allowVirtual,
            'showOnPortal' => $this->showOnPortal,
            'priorityShow' => $this->priorityShow,
            'specialOffer' => $this->specialOffer,
            'isHotOffer' => $this->isHotOffer,
            'companyId' => $this->companyId,
        );

        if (null !== $this->productAttrsByGroup) {
            $cacheParts['productAttrsByGroup'] = $this->productAttrsByGroup;
        }

        return sha1(serialize($cacheParts));
    }

    public function getCacheTags()
    {
        return $this->cacheTags;
    }

    public function clear()
    {
        $this->showOnPortal = null;
        $this->allowVirtual = null;
    }

    public function hasTerritoryFilters(bool $checkCountry = false): bool
    {
        return $this->cityId
            || $this->regionId
            || $this->companyCityId
            || $this->productCityId
            || ($checkCountry && $this->countryId);
    }

    public function resetTerritoryFilters(bool $resetCountry = false)
    {
        $this->cityId(null);
        $this->regionId(null);
        $this->productCityId(null);
        $this->companyCityId(null);
        if ($resetCountry) {
            $this->countryId(null);
        }
    }

    public function diff(ComparableSpec $spec): ?ComparableSpec
    {
        if (!$spec instanceof static) {
            throw new \InvalidArgumentException();
        }

        $hasDifferences = false;
        $diff = new static();
        $diff->clear();

        if ($this->price != $spec->price) {
            $diff->price = $spec->price;
            $hasDifferences = true;
        }

        if ($this->productAttrsByGroup != $spec->productAttrsByGroup) {
            $diff->productAttrsByGroup = $spec->productAttrsByGroup;
            $hasDifferences = true;
        }

        return $hasDifferences ? $diff : null;
    }
}
