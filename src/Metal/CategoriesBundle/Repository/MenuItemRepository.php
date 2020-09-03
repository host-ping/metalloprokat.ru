<?php

namespace Metal\CategoriesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\CategoriesBundle\Entity\MenuItem;

class MenuItemRepository extends EntityRepository
{
    /**
     * @param MenuItem[]|MenuItem|null $items
     *
     * @return MenuItem[]
     */
    public function getChildrenForItems($items)
    {
        if (array() === $items) {
            return array();
        }

        $qb = $this->_em
            ->createQueryBuilder()
            ->select('mi')
            ->from('MetalCategoriesBundle:MenuItem', 'mi', 'mi.id');

        if (null === $items) {
            $qb
                ->where('mi.parent IS NULL')
                ->andWhere('mi.mode = :label')
                ->setParameter('label', MenuItem::MODE_LABEL);
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
            if ($items && $items->isVirtualReference() && $items->getVirtualChildrenIdsAsArray()) {
                $qb
                    ->where('mi.id IN (:virtual_children_ids)')
                    ->setParameter('virtual_children_ids', $items->getVirtualChildrenIdsAsArray())
                ;
            } else {
                $qb
                    ->where('mi.parent = :parent')
                    ->setParameter('parent', $items);
            }
        }

        return $qb
            ->orderBy('mi.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param MenuItem[] $items Элементы меню. Текущий активный элемент должен быть на последнем месте
     *
     * @return MenuItem[]
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

    public function buildCategoriesByLevels($id = null)
    {
        $qb = $this->_em->getRepository('MetalCategoriesBundle:MenuItem')
            ->createQueryBuilder('menuItem');

        if ($id) {
            $qb->andWhere('menuItem.id <> :id')
                ->setParameter('id', $id);
        }

        $menuItems = $qb->getQuery()->getResult();
        /* @var $menuItems MenuItem[] */

        $menuItemsHierarchy = array();
        foreach ($menuItems as $menuItem) {
            $parent = $menuItem->getParent() ? $menuItem->getParent()->getId() : 0;
            $menuItemsHierarchy[$parent][] = $menuItem;
        }

        $menuItems = array();
        $callback = function($items, $depth = 1) use (&$callback, &$menuItems, $menuItemsHierarchy) {
            foreach ($items as $item) {
                $item->setAttribute('depth', $depth);
                $menuItems[] = $item;
                if (isset($menuItemsHierarchy[$item->getId()])) {
                    $callback($menuItemsHierarchy[$item->getId()], $depth + 1);
                }
            }
        };

        $callback($menuItemsHierarchy[0]);

        return $menuItems;
    }
}
