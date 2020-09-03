<?php

namespace Metal\CategoriesBundle\Indexer;

use Brouzie\Sphinxy\Indexer\DoctrineQbIndexer;

class CategoryIndexer extends DoctrineQbIndexer
{
    protected function getQueryBuilder()
    {
        return $this->em
            ->createQueryBuilder()
            ->select('e.id, e.title, e.title AS category_title, e.slugCombined AS slug_combined')
            ->from('MetalCategoriesBundle:Category', 'e');
    }
}
