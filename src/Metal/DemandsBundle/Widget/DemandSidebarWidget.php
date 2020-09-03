<?php

namespace Metal\DemandsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Brouzie\WidgetsBundle\Cache\CacheProfile;
use Brouzie\WidgetsBundle\Widget\CacheableWidget;
use Doctrine\ORM\EntityManager;
use Metal\AttributesBundle\DataFetching\AttributesFacetResult;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CategoriesBundle\Entity\Category;
use Metal\DemandsBundle\DataFetching\Spec\DemandFacetSpec;
use Metal\DemandsBundle\DataFetching\Spec\DemandFilteringSpec;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\DataFetching\Spec\TaggableCacheableSpec;

class DemandSidebarWidget extends WidgetAbstract implements CacheableWidget
{
    const MAX_RESULTS = 10;

    private $attributesCollection;

    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefined(array('demands_count'))
            ->setDefaults(array('demands_count' => 0));
    }

    public function getCacheProfile()
    {
        $criteria = $this->getCriteria();

        $cacheKey = $criteria->getCacheKey();
        if (!$cacheKey) {
            return null;
        }

        return new CacheProfile(
            array(
                'key' => $cacheKey,
                'options' => $this->options,
            ),
            DataFetcher::TTL_1DAY,
            $criteria instanceof TaggableCacheableSpec ? $criteria->getCacheTags() : array()
        );
    }

    public function getAttributesCollection()
    {
        if (null !== $this->attributesCollection) {
            return $this->attributesCollection;
        }

        $request = $this->getRequest();
        $category = $request->attributes->get('category');

        $em = $this->container->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManager */
        $attributeValueRepository = $em->getRepository('MetalAttributesBundle:AttributeValue');
        $attributeCategoryRepository = $em->getRepository('MetalAttributesBundle:AttributeCategory');

        $attributes = $attributeCategoryRepository->getAttributesForCategory($category);

        if (!count($attributes)) {
            // если к данной категории не привязаны атрибуты - нет смысла строить фасеты
            return $this->attributesCollection = new AttributesCollection();
        }

        $criteria = $this->getCriteria();

        $dataFetcher = $this->container->get('metal.demands.data_fetcher');

        $facetSpec = new DemandFacetSpec();
        if ($category->getAllowProducts()) {
            foreach ($attributes as $attribute) {
                $facetSpec->facetByAttribute($attribute, $category, $criteria, self::MAX_RESULTS + 1);
            }
        }

        if (!count($facetSpec->facets)) {
            // если нет ни одного фасета - не делаем запросов
            return $this->attributesCollection = new AttributesCollection();
        }

        $attributeToResultSetName = array();
        foreach ($attributes as $attribute) {
            $attributeToResultSetName[$attribute->getId()] = AttributesFacetResult::RESULT_SET_NAME_PREFIX.$category->getId().'.'.$attribute->getId();
        }

        $facetsResultSet = $dataFetcher->getFacetedResultSetByCriteria($criteria, $facetSpec);
        $attributesCollection = $attributeValueRepository->loadCollectionByFacetResult(
            new AttributesFacetResult($facetsResultSet, $attributeToResultSetName)
        );

        return $this->attributesCollection = $attributesCollection;
    }

    /**
     * @return DemandFilteringSpec
     */
    public function getCriteria()
    {
        $request = $this->getRequest();
        $category = $request->attributes->get('category');
        /* @var $category Category */

        $criteria = DemandFilteringSpec::createFromRequest($request);
        if ($category) {
            $criteria->concreteCategory($category);
        }

        return $criteria;
    }
}
