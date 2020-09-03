<?php

namespace Metal\CatalogBundle\Controller;

use Metal\CatalogBundle\Entity\Manufacturer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ManufacturerController extends Controller
{
    /**
     * @ParamConverter("manufacturer", class="MetalCatalogBundle:Manufacturer",
     * options={
     *     "repository_method" = "loadManufacturerBySlug",
     *     "map_method_signature" = true
     * })
     */
    public function viewAction(Manufacturer $manufacturer)
    {
        $brands = $this->getDoctrine()
            ->getRepository('MetalCatalogBundle:Brand')
            ->getBrandsByManufacturer($manufacturer);

        return $this->render(
            '@MetalCatalog/Manufacturer/view.html.twig',
            array(
                'manufacturer' => $manufacturer,
                'brands' => $brands
            )
        );
    }
}
