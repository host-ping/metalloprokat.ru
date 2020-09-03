<?php

namespace Metal\CatalogBundle\DataFetching;

use Doctrine\ORM\EntityManagerInterface;
use Metal\CatalogBundle\Entity\Manufacturer;
use Metal\ProjectBundle\DataFetching\ConcreteEntityLoader;
use Metal\ProjectBundle\DataFetching\Spec\LoadingSpec;
use Metal\ProjectBundle\Util\ArrayUtil;

class CatalogManufacturersEntityLoader implements ConcreteEntityLoader
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getEntitiesByRows(\Traversable $rows, LoadingSpec $options = null)
    {
        $rows = iterator_to_array($rows);
        $manufacturersIds = array_column($rows, 'manufacturer_id');

        $manufacturerRepository = $this->em->getRepository('MetalCatalogBundle:Manufacturer');
        $manufacturers = $manufacturerRepository->findByIds($manufacturersIds);
        /* @var $manufacturers Manufacturer[] */

        $manufacturerRepository->attachBrandsToManufacturers(
            $manufacturers,
            ArrayUtil::mergeGroupConcatResult($rows, 'brands_ids', 5)
        );

        return $manufacturers;
    }
}
