<?php

namespace Metal\StatisticBundle\Repository;

use Doctrine\DBAL\Connection;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProjectBundle\Doctrine\Utils;
use Metal\ProjectBundle\Util\InsertUtil;
use Metal\StatisticBundle\DataFetching\UpdateStatsSpec;

class StatsDailyRepository extends ClientStatsRepository
{
    public function updateUsersOnSiteCounter(UpdateStatsSpec $updateStatsSpec)
    {
        $connection = $this->_em->getConnection();

        if ($updateStatsSpec->isReset()) {
            $resetQb = $connection->createQueryBuilder()
                ->update('stats_daily', 'sd')
                ->set('users_on_site_count', 0);

            if ($updateStatsSpec->companiesIds) {
                $resetQb
                    ->andWhere('sd.company_id IN (:companies_ids)')
                    ->setParameter('companies_ids', $updateStatsSpec->companiesIds, Connection::PARAM_INT_ARRAY);
            }

            $resetQb->execute();
        }

        $qb = $connection->createQueryBuilder()
            ->select('uv.company_id AS company_id')
            ->addSelect('COUNT(uv.user_id) AS users_on_site_count')
            ->addSelect('uv.date AS date')
            ->from('user_visiting', 'uv')
            ->where('uv.company_id IS NOT NULL')
        ;

        if ($updateStatsSpec->dateStart instanceof \DateTime) {
            $qb->andWhere('uv.date >= :date_from')
                ->setParameter('date_from', $updateStatsSpec->dateStart, 'date');
        }

        if ($updateStatsSpec->companiesIds) {
            $qb->andWhere('uv.company_id IN (:companies_ids)')
                ->setParameter('companies_ids', $updateStatsSpec->companiesIds, Connection::PARAM_INT_ARRAY);
        }

        $dataToInsert = $qb->groupBy('uv.company_id')->addGroupBy('uv.date')->execute()->fetchAll();

        if ($dataToInsert) {
            InsertUtil::insertMultipleOrUpdate($connection, 'stats_daily', $dataToInsert, array('users_on_site_count'), 500);
        }
    }

    public function getProductsChangesDateRange(Connection $connection, array $companiesIds = array())
    {
        if (!$connection) {
            $connection = $this->_em->getConnection();
        }

        $qb = $connection->createQueryBuilder()
            ->select('MIN(spc.date_created_at) AS _min, MAX(spc.date_created_at) AS _max')
            ->from('stats_product_change', 'spc')
        ;

        if ($companiesIds) {
            $qb->where('spc.company_id IN(:companiesIds)')
                ->setParameter('companiesIds', $companiesIds, Connection::PARAM_INT_ARRAY);
        }

        return $qb->execute()->fetch();
    }

    public function getStatsProductsCount($date, Connection $connection = null)
    {
        if (!$connection) {
            $connection = $this->_em->getConnection();
        }

        return $connection->fetchAll(
            '
            SELECT
                spc.company_id,
                COUNT(IF(spc.is_added = 0, 1, NULL)) products_updated_count,
                COUNT(IF(spc.is_added = 1, 1, NULL)) products_added_count,
                spc.date_created_at
            FROM stats_product_change AS spc
            WHERE spc.date_created_at = :from_date
            GROUP BY spc.company_id, spc.date_created_at
          ', array('from_date' => $date)
        );
    }

    public function updateProductsChangesStats(UpdateStatsSpec $spec, Connection $conn = null)
    {
        $connWrite = $this->_em->getConnection();
        $conn = $conn ?: $connWrite;

        //TODO: dateFrom/dateTo - могут быть опциональными по идее

        if ($spec->isReset()) {
            Utils::checkConnection($connWrite);
            $resetQb = $connWrite->createQueryBuilder()
                ->update('stats_daily', 'sd')
                ->set('updated_products_count', 0)
                ->set('added_products_count', 0)
                ->where('sd.date >= :date_from')
                ->setParameter('date_from', $spec->dateFrom, 'date')
                ->andWhere('sd.date <= :date_to')
                ->setParameter('date_to', $spec->dateTo, 'date');

            if ($spec->companiesIds) {
                $resetQb
                    ->andWhere('sd.company_id IN (:companies_ids)')
                    ->setParameter('companies_ids', $spec->companiesIds, Connection::PARAM_INT_ARRAY);
            }

            $resetQb->execute();
        }

        Utils::checkConnection($conn);
        $sqQb = $conn->createQueryBuilder()
            ->select('spc.company_id')
            ->addSelect('COUNT(IF(spc.is_added = 0, 1, NULL)) updated_products_count')
            ->addSelect('COUNT(IF(spc.is_added = 1, 1, NULL)) added_products_count')
            ->addSelect('spc.date_created_at AS date')
            ->from('stats_product_change', 'spc')
            ->where('spc.date_created_at >= :date_from')
            ->setParameter('date_from', $spec->dateFrom, 'date')
            ->andWhere('spc.date_created_at <= :date_to')
            ->setParameter('date_to', $spec->dateTo, 'date')
            ->groupBy('spc.company_id')
            ->addGroupBy('spc.date_created_at');

        if ($spec->companiesIds) {
            $sqQb
                ->andWhere('spc.company_id IN (:companies_ids)')
                ->setParameter('companies_ids', $spec->companiesIds, Connection::PARAM_INT_ARRAY);
        }

        $table = $this->getClassMetadata()->getTableName();
        $fields = array('updated_products_count', 'added_products_count');
        $data = $sqQb->execute()->fetchAll();

        Utils::checkConnection($connWrite);
        InsertUtil::insertMultipleOrUpdate($connWrite, $table, $data, $fields, 1000);

        return count($data);
    }

    /**
     * @param Company $company
     * @return array
     */
    public function getCompanyStatistics(Company $company)
    {
        return $this->createQueryBuilder('sd')
            ->select('COALESCE(SUM(sd.reviewsProductsCount), 0) as reviewsProductsCount')
            ->addSelect('COALESCE(SUM(sd.demandsCount), 0) as demandsCount')
            ->addSelect('COALESCE(SUM(sd.callbacksCount), 0) as callbacksCount')
            ->andWhere('sd.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getSingleResult();
    }
}
