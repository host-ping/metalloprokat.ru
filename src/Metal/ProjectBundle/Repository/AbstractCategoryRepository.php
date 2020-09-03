<?php

namespace Metal\ProjectBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\CompaniesBundle\Entity\CustomCompanyCategory;
use Metal\ContentBundle\Entity\Category as ContentCategory;
use Metal\CategoriesBundle\Entity\Category;

class AbstractCategoryRepository extends EntityRepository
{
    public function buildCategoriesByLevels($id = null, $realCategory = false)
    {
        $qb = $this
            ->createQueryBuilder('c');

        if ($id) {
            $qb->andWhere('c.id <> :id')
                ->setParameter('id', $id);
        }

        if ($realCategory) {
            $qb->andWhere('c.virtual = false');
        }

        $categories = $qb->getQuery()->getResult();
        /* @var $categories Category[]|ContentCategory[] */

        if (!$categories) {
            return array();
        }

        $categoriesHierarchy = array();
        foreach ($categories as $category) {
            $parent = $category->getParent() ? $category->getParent()->getId() : 0;
            $categoriesHierarchy[$parent][] = $category;
        }

        $categories = array();
        $callback = function ($items, $depth = 1) use (&$callback, &$categories, $categoriesHierarchy) {
            /* @var $items Category[]|ContentCategory[] */
            foreach ($items as $item) {

                $item->setAttribute('depth', $depth);
                $categories[] = $item;
                if (isset($categoriesHierarchy[$item->getId()])) {
                    $callback($categoriesHierarchy[$item->getId()], $depth + 1);
                }
            }
        };

        $callback($categoriesHierarchy[0]);

        return $categories;
    }

    public function refreshDenormalizedData($callback = null)
    {
        $tableName = $this->getClassMetadata()->getTableName();
        $tableId = $this->getClassMetadata()->getColumnName('id');

        $maxCategoryId = $this->_em->getConnection()->fetchColumn('SELECT MAX('.$tableId.') FROM '.$tableName);

        $categoryClosure = call_user_func(array($this->getClassName(), 'getClosureTableName'));
        $categorySlug = call_user_func(array($this->getClassName(), 'getSlugColumnName'));

        $categoriesIds = $this->_em->getConnection()->fetchAll(
            'SELECT '.$tableId.' AS id FROM '.$tableName.' ORDER BY id'
        );
        $categoriesIds = array_column($categoriesIds, 'id');

        $i = 0;
        foreach ($categoriesIds as $categoryId) {
            $i++;

            if (is_callable($callback)) {
                call_user_func($callback, $categoryId, $maxCategoryId);
            }

            if ($tableName == 'custom_company_category') {
                $this->_em->getConnection()->executeUpdate(
                    '
                    UPDATE '.$tableName.' AS category,
                    ( SELECT
                            GROUP_CONCAT(DISTINCT ancestor ORDER BY depth ASC SEPARATOR ",") AS branchIds,
                            c.'.$tableId.' AS categoryId
                        FROM '.$categoryClosure.' AS cc
                        LEFT JOIN '.$tableName.' AS c ON c.'.$tableId.' = cc.ancestor
                        WHERE descendant = :categoryId
                        ORDER BY depth
                    ) AS t
                    SET category.branch_ids = t.branchIds
                    WHERE category.'.$tableId.' = :categoryId
                ',
                    array('categoryId' => $categoryId)
                );
            } else {
                $this->_em->getConnection()->executeUpdate(
                    '
                    UPDATE '.$tableName.' AS category,
                    ( SELECT
                            GROUP_CONCAT(c.'.$categorySlug.' ORDER BY depth DESC SEPARATOR "/") AS slugCombined,
                            GROUP_CONCAT(DISTINCT ancestor ORDER BY depth ASC SEPARATOR ",") AS branchIds,
                            c.'.$tableId.' AS categoryId
                        FROM '.$categoryClosure.' AS cc
                        LEFT JOIN '.$tableName.' AS c ON c.'.$tableId.' = cc.ancestor
                        WHERE descendant = :categoryId
                        ORDER BY depth
                    ) AS t
                    SET category.slug_combined = t.slugCombined, category.branch_ids = t.branchIds
                    WHERE category.'.$tableId.' = :categoryId
                ',
                    array('categoryId' => $categoryId)
                );
            }
        }
    }

    /**
     * @param Category[]|CustomCompanyCategory[] $categories
     *
     * @return array
     */
    public function serializeCategories(array $categories)
    {
        if (!$categories) {
            return array();
        }

        $categoriesHierarchy = array();
        foreach ($categories as $category) {
            $categoriesHierarchy[(int) $category->getParentId()][] = $category;
        }

        /**
         * @param Category[]|CustomCompanyCategory[] $categories
         *
         * @return array
         */
        $serializer = function ($categories) use (&$serializer, $categoriesHierarchy) {
            $serialized = array();
            foreach ($categories as $category) {
                $serializedCategory = array(
                    'id' => $category->getId(),
                    'title' => $category->getTitle(),
                    'nodes' => array(),
                    'collapsed' => false
                );

                if ($category instanceof Category) {
                    $serializedCategory['allowProducts'] = $category->getAllowProducts();
                }
                if ($category instanceof CustomCompanyCategory) {
                    $serializedCategory['_isCustom'] = true;
                }
                $id = $category->getId();
                if (isset($categoriesHierarchy[$id])) {
                    $serializedCategory['nodes'] = $serializer($categoriesHierarchy[$id]);
                    $serializedCategory['collapsed'] = true;
                }
                $serialized[] = $serializedCategory;
            }

            return $serialized;
        };

        $categories = $serializer($categoriesHierarchy[0]);

        return $categories;
    }

    /**
     * @param Category[]|CustomCompanyCategory[] $categories
     *
     * @return array
     */
    public function serializeAndFlattenCategories(array $categories)
    {
        if (!$categories) {
            return array();
        }

        $categoriesHierarchy = array();
        foreach ($categories as $category) {
            $categoriesHierarchy[(int) $category->getParentId()][] = $category;
        }

        /**
         * @param Category[]|CustomCompanyCategory[] $categories
         *
         * @return array
         */
        $serializer = function ($categories, $depth = 1) use (&$serializer, $categoriesHierarchy) {
            $serialized = array();

            foreach ($categories as $category) {
                $id = $category->getId();
                $serializedCategory = array(
                    'id' => $id,
                    'parentId' => $category->getParentId(),
                    'title' => $category->getTitle(),
                    'depth' => $depth,
                );

                if ($category instanceof Category) {
                    $serializedCategory['allowProducts'] = $category->getAllowProducts();
                }

                if ($category instanceof CustomCompanyCategory) {
                    $serializedCategory['isUserDefined'] = true;
                }

                $serialized[] = $serializedCategory;

                if (isset($categoriesHierarchy[$id])) {
                    $serialized = array_merge($serialized, $serializer($categoriesHierarchy[$id], $depth + 1));
                }
            }

            return $serialized;
        };

        return $serializer($categoriesHierarchy[0]);
    }
}
