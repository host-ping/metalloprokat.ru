<?php

namespace Metal\CorpsiteBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Doctrine\ORM\EntityManager;
use Metal\CorpsiteBundle\Form\PackageOrderType;
use Metal\ServicesBundle\Entity\PackageOrder;
use Metal\ServicesBundle\Entity\Package;

class OrderPackageWidget extends WidgetAbstract
{
    protected function setDefaultOptions()
    {
        $this->optionsResolver
            ->setRequired(array('route'))
        ;
    }

    protected function getParametersToRender()
    {
        $packageOrder = new PackageOrder();
        $orderPackageForm = $this->createForm(
            new PackageOrderType(),
            $packageOrder,
            array('city_repository' => $this->container->get('doctrine')->getRepository('MetalTerritorialBundle:City'))
        );

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $allPackages = $em->getRepository('MetalServicesBundle:Package')->getPackages(true);

        $packages = array();
        foreach ($allPackages as $package) {
            /* @var $package Package */
            $packages[$package->getId()] = $package->getAllPricesByPeriod();
        }

        return array(
            'form' => $orderPackageForm->createView(),
            'packages' => $packages
        );
    }
}
