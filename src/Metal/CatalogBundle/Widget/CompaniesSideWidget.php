<?php

namespace Metal\CatalogBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Doctrine\ORM\EntityManager;
use Metal\CatalogBundle\Entity\Brand;
use Metal\CatalogBundle\Entity\Manufacturer;
use Metal\CatalogBundle\Entity\Product;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\TerritorialBundle\Entity\City;

class CompaniesSideWidget extends WidgetAbstract
{
    private $companiesViewModel;

    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefaults(
                array(
                    'brand' => null,
                    'product' => null,
                    'manufacturer' => null,
                )
            )
            ->setDefined(array('city'))
            ->setAllowedTypes('city', array(City::class, 'null'))
        ;
    }

    public function getParametersToRender()
    {
        return array('companiesViewModel' => $this->getCompaniesViewModel());
    }

    public function hasCompaniesToDisplay() {

        return $this->getCompaniesViewModel()->count > 0;

    }

    private function getCompaniesViewModel()
    {
        if ($this->companiesViewModel !== null) {
            return $this->companiesViewModel;
        }

        $brand = $this->options['brand'];
        /* @var $brand Brand */

        $manufacturer = $this->options['manufacturer'];
        /* @var $manufacturer Manufacturer */

        $product = $this->options['product'];
        /* @var $product Product */

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $specification = new ProductsFilteringSpec();

        if ($brand) {
            $key = $em->getRepository('MetalAttributesBundle:Attribute')->findOneBy(array('code' => 'brand'))->getId();
            $specification->productAttrsByGroup(array($key => array($brand->getId())));
        } elseif ($manufacturer) {
            $key = $em->getRepository('MetalAttributesBundle:Attribute')->findOneBy(array('code' => 'manufacturer'))->getId();
            $specification->productAttrsByGroup(array($key => array($manufacturer->getId())));
        } elseif ($product) {
            $key = $product->getBrand()->getAttribute()->getId();
            $specification->productAttrsByGroup(array($key => array($product->getBrand()->getId())));
            $specification->category($product->getCategory());
        }

        if ($this->options['city']) {
            $specification->city($this->options['city']);
        }

        $specification
            ->allowVirtual(true)
            ->loadCompanies(true);

        $limit = 20;
        $dataFetcher = $this->container->get('metal.products.data_fetcher');
        $entityLoader = $this->container->get('metal.products.companies_entity_loader');

        $pagerfanta = $dataFetcher->getPagerfantaByCriteria($specification, null, $limit);
        $companiesViewModel = $entityLoader->getListResultsViewModel($pagerfanta);
        $this->companiesViewModel = $companiesViewModel;

        return $this->companiesViewModel;
    }
}
