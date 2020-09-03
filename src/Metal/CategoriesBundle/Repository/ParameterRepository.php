<?php

namespace Metal\CategoriesBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Metal\CategoriesBundle\Entity\Category;

class ParameterRepository extends EntityRepository
{
    public function getParametersByCategoryAndSlugs(Category $category, array $slugs)
    {
        return $this
            ->createQueryBuilder('p')
            ->join('p.parameterOption', 'po')
            ->join('p.parameterGroup', 'pg')
            ->addSelect('po')
            ->andWhere('po.slug IN (:parts)')
            ->addSelect('pg')
            ->andWhere('pg.category = :category')
            ->addOrderBy('pg.priority')
            ->addOrderBy('po.minisitePriority')
            ->setParameter('category', $category->getRealCategoryId())
            // явно указываем, что нужно биндить как строку, иначе для числовых слагов, наподобие "12" будут неправильно находиться соответствия
            ->setParameter('parts', $slugs, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getArrayResult();
    }

    public function getParametersBySlugs(array $slugs)
    {
        return $this
            ->createQueryBuilder('p')
            ->join('p.parameterOption', 'po')
            ->join('p.parameterGroup', 'pg')
            ->addSelect('po')
            ->andWhere('po.slug IN (:parts)')
            ->addSelect('pg')
            ->addOrderBy('pg.priority')
            ->addOrderBy('po.minisitePriority')
            // явно указываем, что нужно биндить как строку, иначе для числовых слагов, наподобие "12" будут неправильно находиться соответствия
            ->setParameter('parts', $slugs, Connection::PARAM_STR_ARRAY)
            ->groupBy('p.parameterOption')
            ->getQuery()
            ->getArrayResult();
    }
}
