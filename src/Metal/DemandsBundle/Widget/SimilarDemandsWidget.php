<?php

namespace Metal\DemandsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;

use Metal\CategoriesBundle\Entity\Category;
use Metal\DemandsBundle\DataFetching\Spec\DemandFilteringSpec;
use Metal\DemandsBundle\DataFetching\Spec\DemandLoadingSpec;
use Metal\DemandsBundle\DataFetching\Spec\DemandOrderingSpec;
use Metal\DemandsBundle\Entity\Demand;
use Metal\ProjectBundle\DataFetching\ListResultsViewModel;

class SimilarDemandsWidget extends WidgetAbstract
{
    protected $demandsListResultsViewModel;

    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('demand'))
            ->setRequired(array('category'))
            ->setDefined(array('page'))
            ->setDefined(array('limit'))
            ->setDefined(array('city'))
            ->setDefined(array('country'))
            ->setAllowedTypes('demand', array(Demand::class))
            ->setAllowedTypes('category', array(Category::class))
            ->setDefaults(array('limit' => 20, 'page' => 1));
    }

    public function getParametersToRender()
    {
        return array(
            'pagerfanta' => $this->getDemandsListResultsViewModel()->pagerfanta,
        );
    }

    public function getDemandsCount()
    {
        return $this->getDemandsListResultsViewModel()->count;
    }

    /**
     * @return ListResultsViewModel
     */
    private function getDemandsListResultsViewModel()
    {
        if (null !== $this->demandsListResultsViewModel) {
            return $this->demandsListResultsViewModel;
        }

        $demand = $this->options['demand'];
        /* @var  $demand Demand */

        $category = $this->options['category'];
        /* @var  $category Category*/

        $demandsDataFetcher = $this->container->get('metal.demands.data_fetcher');
        $demandsEntityLoader = $this->container->get('metal.demands.entity_loader');

        $criteria = new DemandFilteringSpec();
        $orderBy = new DemandOrderingSpec();

        $page = $this->options['page'];
        $limit = $this->options['limit'];

        $criteria->category($category)->noId($demand->getId());

        if (!empty($this->options['city'])) {
            $criteria->city($this->options['city']);
        } elseif (!empty($this->options['country'])) {
            $criteria->country($this->options['country']);
        }

        $orderBy->createdAt();
        $loaderOpts = new DemandLoadingSpec();
        $loaderOpts->attachDemandFiles(false);

        $pagerfanta = $demandsDataFetcher->getPagerfantaByCriteria($criteria, $orderBy, $limit, $page);
        $demandsListResultsViewModel = $demandsEntityLoader->getListResultsViewModel($pagerfanta, $loaderOpts);

        $demands = iterator_to_array($demandsListResultsViewModel->pagerfanta);

        $this->getDoctrine()->getRepository('MetalDemandsBundle:DemandCategory')->attachToDemands($demands);
        $this->getDoctrine()->getRepository('MetalDemandsBundle:DemandItem')->attachDemandItems($demands);

        return $this->demandsListResultsViewModel = $demandsListResultsViewModel;
    }
}
