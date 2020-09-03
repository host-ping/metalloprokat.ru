<?php

namespace Metal\CatalogBundle\DataFetching;

use Doctrine\ORM\EntityManagerInterface;
use Metal\CatalogBundle\Entity\Brand;
use Metal\ProjectBundle\DataFetching\ConcreteEntityLoader;
use Metal\ProjectBundle\DataFetching\Spec\LoadingSpec;
use Metal\ProjectBundle\Util\ArrayUtil;

class CatalogBrandsEntityLoader implements ConcreteEntityLoader
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
        $brandsIds = array_column($rows, 'brand_id');

        $brandRepository = $this->em->getRepository('MetalCatalogBundle:Brand');
        $brands = $brandRepository->findByIds($brandsIds);
        /* @var $products Brand[] */

        $brandRepository->attachProductsToBrands($brands, ArrayUtil::mergeGroupConcatResult($rows, 'products_ids', 5));

        return $brands;
    }
}
