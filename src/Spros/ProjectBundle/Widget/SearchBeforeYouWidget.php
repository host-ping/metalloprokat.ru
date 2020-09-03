<?php

namespace Spros\ProjectBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;

use Doctrine\ORM\EntityManager;
use Metal\AttributesBundle\DataFetching\AttributesFacetResult;
use Metal\AttributesBundle\Entity\Attribute;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFacetSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\TerritorialBundle\Entity\City;

class SearchBeforeYouWidget extends WidgetAbstract
{
    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('categories'))
            ->setDefined(array('category', 'city', 'attributes_collection'))
            ->setAllowedTypes('category', array(Category::class, 'null'))
            ->setAllowedTypes('city', array(City::class, 'null'))
            ->setDefaults(array('limit' => 3))
        ;
    }

    protected function getParametersToRender()
    {
        $em = $this->getDoctrine()->getManager();
        $category = $this->options['category'];
        $categories = $this->options['categories'];
        $city = $this->options['city'];
        $limit = $this->options['limit'];
        $attributesCollection = $this->options['attributes_collection'];
        /* @var $attributesCollection AttributesCollection */

        if (null === $category) {
            $category = current($categories);
        }

        // для главной берем Мск
        if (null === $city) {
            $cityRepository = $em->getRepository('MetalTerritorialBundle:City');
            /* @var $cityRepository \Metal\TerritorialBundle\Repository\CityRepository */
            $city = $cityRepository->findOneBy(array('slug' => 'msk'));
        }

        if ($category->getAttribute('hasChildren')) {
            $category = current($category->getAttribute('children'));
        }

        $attributeValueRepository = $em->getRepository('MetalAttributesBundle:AttributeValue');
        $newAttributesCollection = $this->getAttributesCollection($category);

        // получаем комбинацию параметров текущей страницы parametersDefault и исключаем их
        $currentParametersIds = $attributesCollection->getAttributesValuesIds();
        sort($currentParametersIds);

        $attributesCollectionCollection = array();
        for ($i = 0; $i < $limit; $i++) {
            $attributesValuesIds = $attributesValuesSlugs = array();
            foreach ($newAttributesCollection as $attributeValues) {
                $attributeValues = array_values($attributeValues);
                $parametersForLink = $attributeValues[($city->getId() + array_sum($currentParametersIds) + $i) % count($attributeValues)];
                $attributesValuesIds[] = $parametersForLink->getId();
                $attributesValuesSlugs[] = $parametersForLink->getSlug();
            }

            sort($attributesValuesIds);

            if ($currentParametersIds != $attributesValuesIds) {
                $attributesCollectionCollection[] = $attributeValueRepository
                    ->loadCollectionBySlugs($category, $attributesValuesSlugs);
            }
        }

        return compact('category', 'city', 'attributesCollectionCollection');
    }

    private function getAttributesCollection(Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $criteria = ProductsFilteringSpec::createFromRequest($this->getRequest())
            ->category($category)
            ->productAttrsByGroup(null)
        ;

        $attributes = $em->getRepository('MetalAttributesBundle:AttributeCategory')
            ->getAttributesForCategory($category);

        $facetSpec = new ProductsFacetSpec();
        foreach ($attributes as $attribute) {
            $facetSpec->facetByAttribute($attribute, $criteria);
        }

        $dataFetcher = $this->container->get('metal.products.data_fetcher');

        $facetsResultSet = $dataFetcher->getFacetedResultSetByCriteria($criteria, $facetSpec, DataFetcher::TTL_5DAYS);

        $attributeValueRepository = $em->getRepository('MetalAttributesBundle:AttributeValue');
        $attributesCollection = $attributeValueRepository->loadCollectionByFacetResult(
            new AttributesFacetResult($facetsResultSet, $attributes),
            array(Attribute::ORDER_OUTPUT_PRIORITY, AttributeValue::ORDER_OUTPUT_PRIORITY)
        );

        return  $attributesCollection;
    }
}
