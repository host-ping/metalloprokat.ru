<?php

namespace Metal\StatisticBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Connection;
use Metal\ProductsBundle\Entity\Product;

class StatsProductChangeRepository extends EntityRepository
{
    public function insertProductChanges($companyId, $productId, $isAdded)
    {
        //TODO Может накопить продуктов и делать 1 запрос?
        $this->_em->getConnection()->executeUpdate(
            'INSERT IGNORE INTO stats_product_change (date_created_at, product_id, company_id, is_added)
                     VALUES (:date_created, :product_id, :company_id, :is_added)',
            array(
                'date_created' => date('Y-m-d'),
                'product_id' => $productId,
                'company_id' => $companyId,
                'is_added' => $isAdded
            )
        );
    }

    public function updateProductChanges($companyId)
    {
        $this->_em->getConnection()->executeUpdate(
            'INSERT IGNORE INTO stats_product_change (date_created_at, product_id, company_id, is_added)
                SELECT LastUpdated, Message_ID, Company_ID, 0
                FROM Message142 p
                WHERE p.Company_ID = :id
                AND p.Checked NOT IN(:statuses)',
            array(
                'id' => $companyId,
                'statuses' => array(Product::STATUS_DELETED, Product::STATUS_PENDING_CATEGORY_DETECTION)
            ),
            array(
                'statuses' => Connection::PARAM_INT_ARRAY
            )
        );
    }
}
