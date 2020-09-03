<?php

namespace Metal\CatalogBundle\Repository;

use Metal\CatalogBundle\Entity\Manufacturer;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\ProjectBundle\Doctrine\EntityRepository;

class ManufacturerRepository extends EntityRepository
{
    public function loadManufacturerBySlug($slug)
    {
        $manufacturer = $this
            ->createQueryBuilder('m')
            ->andWhere('m.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        return $manufacturer;
    }

    /**
     * @param Manufacturer[] $manufacturers
     * @param array $brandsIds
     */
    public function attachBrandsToManufacturers($manufacturers, array $brandsIds)
    {
        if (!count($manufacturers)) {
            return;
        }

        $brands = $this->_em
            ->getRepository('MetalCatalogBundle:Brand')
            ->findBy(array('id' => $brandsIds));

        $brandsPerManufacturers = array();
        foreach ($brands as $brand) {
            $brandsPerManufacturers[$brand->getManufacturer()->getId()][] = $brand;
        }

        foreach ($manufacturers as $manufacturer) {
            $id = $manufacturer->getId();
            $manufacturer->setAttribute(
                'manufacturer_brands',
                isset($brandsPerManufacturers[$id]) ? $brandsPerManufacturers[$id] : array()
            );
        }
    }

    /**
     * @param FacetResultExtractor $facetResultExtractor
     *
     * @return Manufacturer[]
     */
    public function loadByFacetResult(FacetResultExtractor $facetResultExtractor)
    {
        return $this->findByIds($facetResultExtractor->getIds(), true);
    }
}
