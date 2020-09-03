<?php

namespace Metal\CompaniesBundle\Repository;

use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CustomCompanyCategory;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\Repository\AbstractCategoryRepository;

class CustomCompanyCategoryRepository extends AbstractCategoryRepository
{
    public function calculateCanCompanyHasCustomCategory($company)
    {
        $conn = $this->_em->getConnection();

        $companyId = $company instanceof Company ? $company->getId() : $company;

        $productsCount = $conn->fetchColumn(
            'SELECT 1 FROM Message142 WHERE Company_ID = :company_id AND custom_category_id IS NOT NULL
              AND Checked = :status LIMIT 1',
            array('company_id' => $companyId, 'status' => Product::STATUS_CHECKED)
        );

        $conn->executeUpdate(
            'UPDATE company_minisite SET has_custom_category = :has_custom_category WHERE company_id = :company_id',
            array('company_id' => $companyId, 'has_custom_category' => $productsCount ? true : false)
        );
    }

    /**
     * @param $company
     *
     * @return CustomCompanyCategory[]
     */
    public function getCategoriesForCompany($company)
    {
        return $this
            ->createQueryBuilder('ccc', 'ccc.id')
            ->andWhere('ccc.company = :company')
            ->setParameter('company', $company)
            ->orderBy('ccc.displayPosition', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $categories
     * @param int $parentSequence
     * @param int $sequence
     *
     * @return array
     */
    public function flattenCategoriesHierarchy(array $categories, $parentSequence = null, &$sequence = 0)
    {
        $result = array();
        foreach ($categories as $category) {
            $sequence++;

            $result[$sequence] = array(
                'id' => !empty($category['id']) ? $category['id'] : null,
                'sequence' => $sequence,
                'parent_sequence' => $parentSequence,
                'title' => trim($category['title']),
            );

            if (!empty($category['nodes'])) {
                $childNodes = $this->flattenCategoriesHierarchy($category['nodes'], $sequence, $sequence);
                $result = array_replace($result, $childNodes);
            }
        }

        return $result;
    }
}
