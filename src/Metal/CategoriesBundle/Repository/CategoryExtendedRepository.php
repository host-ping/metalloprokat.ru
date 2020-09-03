<?php

namespace Metal\CategoriesBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CategoryExtendedRepository extends EntityRepository
{
    /**
     * @var array
     */
    protected $loadedExtendedPatterns;

    public function getCategoriesExtendedPatterns()
    {
        if (null !== $this->loadedExtendedPatterns) {
            return $this->loadedExtendedPatterns;
        }

        $this->loadedExtendedPatterns = $this->_em->createQueryBuilder()
            ->select('categoryExtended.extendedPattern AS extendedPattern')
            ->addSelect('category.id')
            ->from('MetalCategoriesBundle:Category', 'category', 'category.id')
            ->join('category.categoryExtended', 'categoryExtended')
            ->andWhere('category.allowProducts = true')
            ->andWhere('categoryExtended.extendedPattern != :empty')
            ->setParameter('empty', '')
            ->addOrderBy('categoryExtended.matchingPriority', 'DESC')
            ->getQuery()
            ->getArrayResult();

        foreach ($this->loadedExtendedPatterns as $id => $extendedPatterns) {
            $this->loadedExtendedPatterns[$id] = $extendedPatterns['extendedPattern'];
        }

        return $this->loadedExtendedPatterns;
    }

    public function getCategoryExtendedPattern($categoryId)
    {
        $this->getCategoriesExtendedPatterns();

        if (isset($this->loadedExtendedPatterns[$categoryId])) {
            return $this->loadedExtendedPatterns[$categoryId];
        }

        throw new \InvalidArgumentException(sprintf('No extended pattern for category.id "%d"', $categoryId));
    }
}