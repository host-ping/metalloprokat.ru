<?php

namespace Spros\ProjectBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProjectBundle\Helper\CounterHelper;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;

class NeighbouringCitiesWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefined(array('category', 'city', 'attributes_collection'))
            ->setAllowedTypes('category', array(Category::class, 'null'))
            ->setAllowedTypes('city', array(City::class, 'null'))
            ->setDefaults(array('limit' => 3));
    }

    public function getParametersToRender()
    {
        $category = $this->options['category'];
        /* @var $category Category */
        $country = $this->getRequest()->attributes->get('country');

        /* @var $country Country */
        $city = $this->options['city'];
        /* @var $city City */
        $limit = $this->options['limit'];
        $attributesCollection = $this->options['attributes_collection'];

        $counterHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Counter');
        /* @var $counterHelper CounterHelper */

        $criteria['countries_ids'] = $country->getId();
        $criteria['is_virtual'] = false;

        if ($category) {
            $criteria['category_id'] = $category->getId();
        }

        $results = $counterHelper->getItemsCountPerObject('products_count', $criteria, 'cities_ids', array('_count' => 'DESC'), $limit);

        if ($city) {
            unset($results[$city->getId()]);
        }

        $cities = $this->getDoctrine()->getRepository('MetalTerritorialBundle:City')->findByIds(array_keys($results));

        return array(
            'cities' => $cities,
            'category' => $category,
            'attributesCollection' => $attributesCollection,
        );
    }
}
