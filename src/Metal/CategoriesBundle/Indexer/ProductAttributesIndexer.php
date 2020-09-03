<?php

namespace Metal\CategoriesBundle\Indexer;

use Brouzie\Sphinxy\Indexer\DoctrineQbIndexer;

class ProductAttributesIndexer extends DoctrineQbIndexer
{
    protected function getQueryBuilder()
    {
        return $this->em
            ->createQueryBuilder()
            ->select('e.id')
            ->addSelect("CONCAT(po.title, ' ') AS title_field")
            ->addSelect('po.title')
            ->addSelect('po.keyword AS slug')
            ->addSelect('c.id AS category_id')
            ->addSelect('c.title AS category_title')
            ->addSelect('c.slugCombined AS category_slug_combined')
            ->from('MetalCategoriesBundle:Parameter', 'e')
            ->join('e.parameterOption', 'po')
            ->join('e.parameterGroup', 'pg')
            ->join('pg.category', 'c')
        ;
    }
} 