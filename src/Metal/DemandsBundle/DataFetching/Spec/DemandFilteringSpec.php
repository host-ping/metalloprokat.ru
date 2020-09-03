<?php

namespace Metal\DemandsBundle\DataFetching\Spec;

use Metal\AttributesBundle\DataFetching\Spec\AttributeSpec;
use Metal\CategoriesBundle\DataFetching\Spec\CategorySpec;
use Metal\CategoriesBundle\Entity\Category;
use Metal\DemandsBundle\Entity\ValueObject\ConsumerTypeProvider;
use Metal\DemandsBundle\Helper\DefaultHelper;
use Metal\ProjectBundle\DataFetching\Spec\CacheableSpec;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\TerritorialBundle\DataFetching\Spec\TerritorialSpec;
use Symfony\Component\HttpFoundation\Request;

class DemandFilteringSpec extends FilteringSpec implements CacheableSpec
{
    public $id;
    public $noId;
    public $matchTitle;

    /**
     * @var \DateTime
     */
    public $dateFrom;

    /**
     * @var \DateTime
     */
    public $dateTo;

    public $isRepetitive;
    public $isWholesale;
    public $authorType;
    public $territorialStructureIds = array();
    public $categoriesIds = array();
    public $ids = array();

    use TerritorialSpec;

    use CategorySpec;

    use AttributeSpec;

    public function id($id)
    {
        $this->id = $id;

        return $this;
    }

    public function ids(array $ids)
    {
        $this->ids = $ids;

        return $this;
    }

    public function categoriesIds(array $categoriesIds)
    {
        $this->categoriesIds = $categoriesIds;

        return $this;
    }

    public function territorialStructureIds(array $territorialStructureIds)
    {
        $this->territorialStructureIds = $territorialStructureIds;

        return $this;
    }

    public function isRepetitive($isRepetitive)
    {
        $this->isRepetitive = $isRepetitive;

        return $this;
    }

    public function isWholesale($isWholesale)
    {
        $this->isWholesale = $isWholesale;

        return $this;
    }

    public function authorType($authorType)
    {
        $this->authorType = $authorType;

        return $this;
    }

    public function matchTitle($titleToMatch)
    {
        $this->matchTitle = $titleToMatch;

        return $this;
    }

    public function dateFrom(\DateTime $dateFrom = null)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function dateTo(\DateTime $dateTo = null)
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function noId($noId)
    {
        $this->noId = $noId;

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return static
     */
    public static function createFromRequest(Request $request)
    {
        $category = $request->attributes->get('category');
        /* @var $category Category */

        $demandWholesale = $request->query->get('wholesale');
        $demandConsumerType = $request->query->get('consumers');
        $demandPeriodicity = $request->query->get('periodicity');

        $specification = new static();

        $specification
            ->categoryId($category ? $category->getRealCategoryId() : $request->query->get('category'));

        $specification
            ->city($request->attributes->get('city'))
            ->region($request->attributes->get('region'))
            ->country($request->attributes->get('country'));

        if ($demandPeriodicity && $demandPeriodicity !== 'all') {
            $specification->isRepetitive($demandPeriodicity === 'permanent');
        }

        if ($demandWholesale && $demandWholesale != 0) {
            $specification->isWholesale($demandWholesale == '1');
        }

        if ($demandConsumerType === 'consumer') {
            $specification->authorType(ConsumerTypeProvider::CONSUMER);
        } elseif ($demandConsumerType === 'trader') {
            $specification->authorType(ConsumerTypeProvider::TRADER);
        }

        if ($id = $request->query->get('id')) {
            $specification->id($id);
        }

        if ($matchTitle = $request->query->get('q')) {
            $specification->matchTitle($matchTitle);
        }

        list($periodFrom, $periodTo) = DefaultHelper::determinatePeriod($request);
        $specification->dateFrom($periodFrom);
        $specification->dateTo($periodTo);

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
        if (null !== $this->matchTitle || array() !== $this->categoriesIds
            || null !== $this->id || array() !== $this->territorialStructureIds
            || null !== $this->noId || null !== $this->dateFrom || null !== $this->dateTo
        ) {
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
            'cityId' => $this->cityId,
            'regionId' => $this->regionId,
            'countryId' => $this->countryId,
            'categoryId' => $this->categoryId,
            'isRepetitive' => $this->isRepetitive,
            'authorType' => $this->authorType,
            'isWholesale' => $this->isWholesale
        );

        if (null !== $this->productAttrsByGroup) {
            $cacheParts['productAttrsByGroup'] = $this->productAttrsByGroup;
        }

        return sha1(serialize($cacheParts));
    }
}
