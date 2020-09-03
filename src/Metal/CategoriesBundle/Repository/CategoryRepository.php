<?php

namespace Metal\CategoriesBundle\Repository;

use Doctrine\DBAL\Connection;

use Metal\CategoriesBundle\Entity\Category;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\Repository\AbstractCategoryRepository;

class CategoryRepository extends AbstractCategoryRepository
{
    /**
     * TODO: переписать с использованием публичного поля
     * @param Category[] $categories
     *
     * @return Category[]
     */
    public function buildCategoriesHierarchy(array $categories)
    {
        $categoriesDirected = array();
        /* @var $categoriesDirected Category[] */
        foreach ($categories as $category) {
            $categoriesDirected[$category->getId()] = $category;
            $category->setAttribute('hasChildren', false);
        }

        $rootItems = array();
        foreach ($categoriesDirected as $category) {
            if ($parentId = $category->getParentId()) {
                if (!isset($categoriesDirected[$parentId])) {
                    continue; // database collision
                }
                $parent = $categoriesDirected[$parentId];
                if ($children = $parent->getAttribute('children')) {
                    $children[] = $category;
                } else {
                    $children = array($category);
                    $parent->setAttribute('hasChildren', true);
                }

                $parent->setAttribute('children', $children);
            } else {
                $rootItems[] = $category;
            }
        }

        return $rootItems;
    }

    public function getCategoriesAsSimpleArray($indexById = false, $isAllowProducts = true, $isEnabled = true, $companyId = null, $notVirtual = true, $status = null, $hasImage = null)
    {
        $qb = $this
            ->createQueryBuilder('c')
            ->select('c.id, c.title');

        if ($companyId) {
            $qb->join('MetalProductsBundle:Product', 'p', 'WITH', 'c.id = p.category')
                ->andWhere('p.company = :company_id')
                ->setParameter('company_id', $companyId)
                ->groupBy('c.id');

            if ($status !== null) {
                $qb->andWhere('p.checked = :status')
                    ->setParameter('status', $status);
            }

            if ($hasImage) {
                if ($hasImage === 'y') {
                    $qb->andWhere('p.image IS NOT NULL');
                } else {
                    $qb->andWhere('p.image IS NULL');
                }
            }
        }

        if ($isAllowProducts) {
            $qb->andWhere('c.allowProducts = 1');
        }

        if ($isEnabled) {
            $qb->andWhere('c.isEnabled = true');
        }

        if ($notVirtual) {
            $qb->andWhere('c.virtual = false');
        }

        $results = $qb
            ->addOrderBy('c.title')
            ->getQuery()
            ->getResult()
        ;

        $resultsIndexById = array();
        if ($indexById) {
            foreach ($results as $category) {
                $resultsIndexById[$category['id']] = $category['title'];
            }

            return $resultsIndexById;
        }

        return $results;
    }

    public function getStringWithoutIn($utmTerm)
    {
        $utmTerm = preg_replace('/\sв\s.*/', '', $utmTerm);
        $utmTerm = trim($utmTerm);

        return $utmTerm;
    }

    public function getNormalizedString($utmTerm)
    {
        $ignoreWords = array(
            'Производство',
            'где купить',
            'стоимость',
            'Продажа',
            'Купите',
            'купить',
            'Расчет',
            'link1',
            'link2',
            'link3',
            'цена',
        );

        $utmTerm = str_ireplace($ignoreWords, '', $utmTerm);
        $utmTerm = trim($utmTerm);

        return $utmTerm;
    }

    public function refreshCategoryData(Category $category)
    {
        $conn = $this->_em->getConnection();

        $conn->executeUpdate(
            'UPDATE Message73 AS update_category
              JOIN (
                SELECT
                      closure.descendant,
                      GROUP_CONCAT(category.Keyword ORDER BY closure.depth DESC SEPARATOR "/" ) AS new_slug_combined,
                      GROUP_CONCAT(category.Message_ID ORDER BY closure.depth ASC SEPARATOR ",") AS new_branch_ids
                    FROM Message73 AS category
                      JOIN category_closure AS closure ON closure.ancestor = category.Message_ID
                    GROUP BY closure.descendant
                ) AS result ON result.descendant = update_category.Message_ID
            SET
              update_category.slug_combined = result.new_slug_combined,
              update_category.branch_ids = result.new_branch_ids'
        );

        $conn->executeUpdate(
            'UPDATE menu_item AS mi
              JOIN Message73 AS category ON category.Message_ID = mi.category_id
            SET
              mi.slug_combined = category.slug_combined'
        );

        if (!$category->getIsEnabled()) {
            $conn->executeUpdate('DELETE FROM url_rewrite WHERE category_id = :category_id', array('category_id' => $category->getId()));
        } else {
            $conn->executeUpdate('
                INSERT INTO
                  url_rewrite (category_id, path_prefix)
                  SELECT
                    cat.Message_ID,
                    cat.slug_combined
                  FROM
                    Message73 AS cat
                  WHERE
                    cat.Message_ID = :category_id
                ON DUPLICATE KEY UPDATE path_prefix = cat.slug_combined, category_id = cat.Message_ID',
                array('category_id' => $category->getId())
            );
        }

        $conn->executeUpdate('
            UPDATE url_rewrite AS ur
              JOIN Message73 AS cat ON ur.category_id = cat.Message_ID
            SET ur.path_prefix = cat.slug_combined
            ');
    }

    public function getSimpleCategoriesByLevels(array $categoriesIds)
    {
        $categories = $this->_em->getRepository('MetalCategoriesBundle:CategoryClosure')
            ->createQueryBuilder('cc')
            ->join('cc.ancestor', 'cat')
            ->select('cat.id')
            ->addSelect('cat.parentId')
            ->addSelect('cat.title')
            ->where('cc.descendant IN (:categories_ids)')
            ->setParameter('categories_ids', $categoriesIds)
            ->groupBy('cc.ancestor')
            ->orderBy('cat.parentId')
            ->getQuery()
            ->getResult();

        return $categories;
    }

    public function onDeleteCategory()
    {
        $conn = $this->_em->getConnection();

        $conn->executeUpdate(
            "UPDATE Message142 AS p SET p.Checked = :product_status
                WHERE NOT EXISTS(
                SELECT c.Message_ID FROM Message73 AS c WHERE c.Message_ID = p.Category_ID
                ) AND p.Checked NOT IN (:not_statuses)",
            array(
                'product_status' => Product::STATUS_PENDING_CATEGORY_DETECTION,
                'not_statuses'   => array(Product::STATUS_PROCESSING, Product::STATUS_DELETED)
            ),
            array(
                'not_statuses'   => Connection::PARAM_INT_ARRAY
            )
        );
    }
}
