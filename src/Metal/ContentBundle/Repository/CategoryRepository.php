<?php

namespace Metal\ContentBundle\Repository;

use Metal\ContentBundle\Entity\AbstractContentEntry;
use Metal\ContentBundle\Entity\Category;
use Metal\ProjectBundle\Repository\AbstractCategoryRepository;

class CategoryRepository extends AbstractCategoryRepository
{
    /**
     * @param AbstractContentEntry[]|\Traversable $contentEntries
     */
    public function attachCategoriesToContentEntries($contentEntries)
    {
        $categoriesIds = array();
        foreach ($contentEntries as $contentEntry) {
            $categoriesIds[$contentEntry->getCategory()->getId()] = true;
            if ($categorySecondary = $contentEntry->getCategorySecondary()) {
                $categoriesIds[$categorySecondary->getId()] = true;
            }
        }

        if (!$categoriesIds) {
            return;
        }

        $this->findBy(array('id' => array_keys($categoriesIds)));
    }


    /**
     * @param Category[]|Category|null $items
     *
     * @return Category[]
     */
    public function getChildrenForItems($items)
    {
        if (array() === $items) {
            return array();
        }

        $qb = $this->_em
            ->createQueryBuilder()
            ->select('mi')
            ->from('MetalContentBundle:Category', 'mi', 'mi.id');

        if (null === $items) {
            $qb
                ->where('mi.parent IS NULL');
        } elseif (is_array($items)) {
            if (false !== array_search(null, $items, true)) {
                $qb
                    ->where('(mi.parent IN (:parents) OR mi.parent IS NULL)')
                    ->setParameter('parents', $items);
            } else {
                $qb
                    ->where('mi.parent IN (:parents)')
                    ->setParameter('parents', $items);
            }
        } else {
            $qb
                ->where('mi.parent = :parent')
                ->setParameter('parent', $items);
        }

        return $qb
//            ->orderBy('mi.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Category[] $items Элементы меню. Текущий активный элемент должен быть на последнем месте
     *
     * @return Category[]
     */
    public function getSiblingsForBranch($items)
    {
        $items = array_values($items);
        // не нужно грузить детей для последнего элемента ветки
        array_pop($items);
        // при этом нужно загрузить детей для корневого элемента
        array_unshift($items, null);

        return $this->getChildrenForItems($items);
    }
}
