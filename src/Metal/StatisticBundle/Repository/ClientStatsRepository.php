<?php

namespace Metal\StatisticBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use Metal\DemandsBundle\Entity\Demand;
use Metal\ProjectBundle\Doctrine\Utils;
use Metal\ProjectBundle\Util\InsertUtil;
use Metal\StatisticBundle\DataFetching\UpdateStatsSpec;
use Metal\StatisticBundle\Entity\ClientStats;
use Metal\StatisticBundle\Entity\StatsElement;

class ClientStatsRepository extends AbstractStatsRepository
{
    public function truncate()
    {
        $table = $this->getClassMetadata()->getTableName();
        $connection = $this->_em->getConnection();

        $connection->executeUpdate('TRUNCATE '.$table);
    }

    public function getStatsElementDateRange(Connection $connection, array $companiesIds = array())
    {
        if (!$connection) {
            $connection = $this->_em->getConnection();
        }

        $qb = $connection->createQueryBuilder()
            ->select('MIN(se.date_created_at) AS _min, MAX(se.date_created_at) AS _max')
            ->from('stats_element', 'se')
        ;

        if ($companiesIds) {
            $qb->where('se.company_id IN (:companies_ids)')
                ->setParameter('companies_ids', $companiesIds, Connection::PARAM_INT_ARRAY);
        }

        return $qb->execute()->fetch();
    }

    public function updateStatsElementStats(UpdateStatsSpec $updateStatsSpec, Connection $conn = null)
    {
        $connWrite = $this->_em->getConnection();
        $conn = $conn ?: $connWrite;
        $table = $this->getClassMetadata()->getTableName();
        //TODO: dateFrom/dateTo - могут быть опциональными по идее

        if ($updateStatsSpec->isReset()) {
            Utils::checkConnection($connWrite);

            $resetQb = $connWrite->createQueryBuilder()
                ->update($table, 'sd')
                ->set('reviews_phones_count', 0)
                ->set('website_visits_count', 0)
                ->set('reviews_products_count', 0)
                ->set('show_products_count', 0)
                ->where('date >= :date_from')
                ->setParameter('date_from', $updateStatsSpec->dateFrom, 'date')
                ->andWhere('date <= :date_to')
                ->setParameter('date_to', $updateStatsSpec->dateTo, 'date')
            ;

            if ($updateStatsSpec->companiesIds) {
                $resetQb
                    ->andWhere('sd.company_id IN (:companies_ids)')
                    ->setParameter('companies_ids', $updateStatsSpec->companiesIds, Connection::PARAM_INT_ARRAY);
            }

            $resetQb->execute();
        }

        Utils::checkConnection($conn);

        $sqQb = $conn->createQueryBuilder()
            ->select('se.company_id AS company_id')
            ->addSelect('COUNT(IF(se.action = :view_phone, 1, NULL)) AS reviews_phones_count')
            ->addSelect('COUNT(IF(se.action = :view_product, 1, NULL)) AS reviews_products_count')
            ->addSelect('COUNT(IF(se.action = :view_website, 1, NULL)) AS website_visits_count')
            ->addSelect('COUNT(IF(se.action = :show_product, 1, NULL)) AS show_products_count')
            ->from('stats_element', 'se')
            ->where('se.date_created_at >= :date_from')
            ->setParameter('date_from', $updateStatsSpec->dateFrom, 'date')
            ->andWhere('se.date_created_at <= :date_to')
            ->setParameter('date_to', $updateStatsSpec->dateTo, 'date')
            ->groupBy('se.company_id')
            ->setParameter('view_phone', StatsElement::ACTION_VIEW_PHONE, \PDO::PARAM_INT)
            ->setParameter('view_product', StatsElement::ACTION_VIEW_PRODUCT, \PDO::PARAM_INT)
            ->setParameter('view_website', StatsElement::ACTION_GO_TO_WEBSITE, \PDO::PARAM_INT)
            ->setParameter('show_product', StatsElement::ACTION_SHOW_PRODUCT, \PDO::PARAM_INT)
        ;

        switch ($table) {
            case ClientStats::GROUP_BY_DAILY_TABLE:
                $sqQb
                    ->addSelect('se.date_created_at AS date')
                    ->addGroupBy('se.date_created_at');
                break;

            case ClientStats::GROUP_BY_CITY_TABLE:
                $sqQb
                    ->addSelect('se.city_id AS city_id')
                    ->addSelect('se.date_created_at AS date')
                    ->addGroupBy('se.date_created_at')
                    ->addGroupBy('se.city_id');
                break;

            case ClientStats::GROUP_BY_CATEGORY_TABLE:
                $sqQb
                    ->addSelect('IF(se.category_id, se.category_id, NULL) AS category_id')
                    ->addSelect('se.date_created_at AS date')
                    ->addSelect('IFNULL(se.category_id, se.source_type_id * -1) AS aggregation_column')
                    ->addSelect('IF(se.category_id IS NULL, se.source_type_id, 0) AS source_type_id')
                    ->addGroupBy('se.date_created_at')
                    ->addGroupBy('aggregation_column');
                break;

            default:
                throw new \InvalidArgumentException('Unknown grouping');
        }

        if ($updateStatsSpec->companiesIds) {
            $sqQb
                ->andWhere('se.company_id IN (:companies_ids)')
                ->setParameter('companies_ids', $updateStatsSpec->companiesIds, Connection::PARAM_INT_ARRAY)
            ;
        }

        $data = $sqQb->execute()->fetchAll();

        $updateColumns = array(
            'reviews_phones_count',
            'reviews_products_count',
            'website_visits_count',
            'show_products_count'
        );

        Utils::checkConnection($connWrite);
        InsertUtil::insertMultipleOrUpdate($connWrite, $table, $data, $updateColumns, 1000);

        return count($data);
    }

    //,array $companiesIds = array(), \DateTime $dateFrom = null
    public function updateComplaintsCounter(UpdateStatsSpec $updateStatsSpec)
    {
        $table = $this->getClassMetadata()->getTableName();
        $connection = $this->_em->getConnection();

        if ($updateStatsSpec->isReset()) {
            $resetQb = $connection->createQueryBuilder()
                ->update($table, 'sd')
                ->set('complaints_count', 0)
                ->set('complaints_processed_count', 0);

            if ($updateStatsSpec->companiesIds) {
                $resetQb
                    ->andWhere('sd.company_id IN (:companies_ids)')
                    ->setParameter('companies_ids', $updateStatsSpec->companiesIds, Connection::PARAM_INT_ARRAY);
            }

            $resetQb->execute();
        }

        $sqParams = $sqParamsTypes = array();

        $sqQb = $connection->createQueryBuilder()
            ->select('c.company_id')
            ->addSelect('COUNT(c.id) AS complaints_count')
            ->addSelect('COUNT(c.processed_at) AS complaints_processed_count')
            ->from('complaint', 'c')
            ->addGroupBy('c.company_id');

        switch ($table) {
            case ClientStats::GROUP_BY_DAILY_TABLE:
                $column = 'date';
                $sqQb
                    ->addSelect('DATE(c.created_at) AS _date')
                    ->addGroupBy('_date');
                break;

            case ClientStats::GROUP_BY_CITY_TABLE:
                $column = 'city_id, date';
                $sqQb
                    ->addSelect('c.city_id')
                    ->addSelect('DATE(c.created_at) AS _date')
                    ->addGroupBy('_date')
                    ->addGroupBy('c.city_id');
                break;

            case ClientStats::GROUP_BY_CATEGORY_TABLE:
                $column = 'category_id, date';
                $sqQb
                    ->addSelect('c.Category_ID')
                    ->addSelect('DATE(c.created_at) AS _date')
                    ->addGroupBy('_date')
                    ->addGroupBy('c.Category_ID');
                break;

            default:
                throw new \InvalidArgumentException('Unknown grouping');
        }

        if (null !== $updateStatsSpec->dateStart) {
            $sqQb->andWhere('c.created_at >= :date_from');
            $sqParams['date_from'] = $updateStatsSpec->dateStart;
            $sqParamsTypes['date_from'] = 'date';
        }

        if ($updateStatsSpec->companiesIds) {
            $sqQb->andWhere('c.company_id IN (:companies_ids)');
            $sqParams['companies_ids'] = $updateStatsSpec->companiesIds;
            $sqParamsTypes['companies_ids'] = Connection::PARAM_INT_ARRAY;
        }

        $connection->executeUpdate(
            'INSERT INTO '.$table.' (company_id, complaints_count, complaints_processed_count, '.$column.') '.$sqQb->getSQL()
            .' ON DUPLICATE KEY UPDATE complaints_count = VALUES(complaints_count),
             complaints_processed_count = VALUES(complaints_processed_count) ',
            $sqParams,
            $sqParamsTypes
        );
    }

    public function updateCallbacksCounter(UpdateStatsSpec $updateStatsSpec)
    {
        $table = $this->getClassMetadata()->getTableName();
        $connection = $this->_em->getConnection();

        if ($updateStatsSpec->isReset()) {
            $resetQb = $connection->createQueryBuilder()
                ->update($table, 'sd')
                ->set('callback_count', 0)
                ->set('callbacks_processed_count', 0);

            if ($updateStatsSpec->companiesIds) {
                $resetQb
                    ->andWhere('sd.company_id IN (:companies_ids)')
                    ->setParameter('companies_ids', $updateStatsSpec->companiesIds, Connection::PARAM_INT_ARRAY);
            }

            $resetQb->execute();
        }

        $sqParams = $sqParamsTypes = array();

        $sqQb = $connection->createQueryBuilder()
            ->select('c.company_id')
            ->addSelect('COUNT(c.id) AS callback_count')
            ->addSelect('COUNT(c.processed_by) AS callbacks_processed_count')
            ->from('callback', 'c')
            ->groupBy('c.company_id');

        switch ($table) {
            case ClientStats::GROUP_BY_DAILY_TABLE:
                $column = 'date';
                $sqQb
                    ->addSelect('DATE(c.created_at) AS _date')
                    ->addGroupBy('_date');
                break;

            case ClientStats::GROUP_BY_CITY_TABLE:
                $column = 'city_id, date';
                $sqQb
                    ->addSelect('c.city_id')
                    ->addSelect('DATE(c.created_at) AS _date')
                    ->addGroupBy('_date')
                    ->addGroupBy('c.city_id');
                break;

            case ClientStats::GROUP_BY_CATEGORY_TABLE:
                $column = 'category_id, date';
                $sqQb
                    ->addSelect('c.Category_ID')
                    ->addSelect('DATE(c.created_at) AS _date')
                    ->addGroupBy('_date')
                    ->addGroupBy('c.Category_ID');
                break;

            default:
                throw new \InvalidArgumentException('Unknown grouping');
        }

        if (null !== $updateStatsSpec->dateStart) {
            $sqQb->andWhere('c.created_at >= :date_from');
            $sqParams['date_from'] = $updateStatsSpec->dateStart;
            $sqParamsTypes['date_from'] = 'date';
        }

        if ($updateStatsSpec->companiesIds) {
            $sqQb->andWhere('c.company_id IN (:companies_ids)');
            $sqParams['companies_ids'] = $updateStatsSpec->companiesIds;
            $sqParamsTypes['companies_ids'] = Connection::PARAM_INT_ARRAY;

        }

        $connection->executeUpdate(
            'INSERT INTO '.$table.' (company_id, callback_count, callbacks_processed_count, '.$column.') '.$sqQb->getSQL()
            .' ON DUPLICATE KEY UPDATE callback_count = VALUES(callback_count),
             callbacks_processed_count = VALUES(callbacks_processed_count) ',
            $sqParams,
            $sqParamsTypes
        );
    }

    public function updateReviewsCounter(UpdateStatsSpec $updateStatsSpec)
    {
        $table = $this->getClassMetadata()->getTableName();
        $connection = $this->_em->getConnection();

        if ($updateStatsSpec->isReset()) {
            $resetQb = $connection->createQueryBuilder()
                ->update($table, 'sd')
                ->set('reviews_count', 0);

            if ($updateStatsSpec->companiesIds) {
                $resetQb
                    ->andWhere('sd.company_id IN (:companies_ids)')
                    ->setParameter('companies_ids', $updateStatsSpec->companiesIds, Connection::PARAM_INT_ARRAY);
            }

            $resetQb->execute();
        }

        $sqParams = $sqParamsTypes = array();

        $sqQb = $connection->createQueryBuilder()
            ->select('cr.company_id')
            ->addSelect('COUNT(cr.id) AS reviews_count')
            ->from('company_review', 'cr')
            ->groupBy('cr.company_id')
            ->addGroupBy('_date');

        switch ($table) {
            case ClientStats::GROUP_BY_DAILY_TABLE:
                $column = 'date';
                $sqQb
                    ->addSelect('DATE(cr.created_at) AS _date')
                    ->addGroupBy('_date');
                break;

            case ClientStats::GROUP_BY_CITY_TABLE:
                $column = 'city_id, date';
                $sqQb
                    ->addSelect('cr.city_id')
                    ->addSelect('DATE(cr.created_at) AS _date')
                    ->addGroupBy('_date')
                    ->addGroupBy('cr.city_id');
                break;

            case ClientStats::GROUP_BY_CATEGORY_TABLE:
                $column = 'category_id, date';
                $sqQb
                    ->addSelect('cr.Category_ID')
                    ->addSelect('DATE(cr.created_at) AS _date')
                    ->addGroupBy('_date')
                    ->addGroupBy('cr.Category_ID');
                break;

            default:
                throw new \InvalidArgumentException('Unknown grouping');
        }


        if ($updateStatsSpec->companiesIds) {
            $sqQb->andWhere('cr.company_id IN (:companies_ids)');
            $sqParams['companies_ids'] = $updateStatsSpec->companiesIds;
            $sqParamsTypes['companies_ids'] = Connection::PARAM_INT_ARRAY;

        }

        if (null !== $updateStatsSpec->dateStart) {
            $sqQb->andWhere('cr.created_at >= :date_from');
            $sqParams['date_from'] = $updateStatsSpec->dateStart;
            $sqParamsTypes['date_from'] = 'date';
        }

        $connection->executeUpdate(
            'INSERT INTO '.$table.' (company_id, reviews_count, '.$column.') '.$sqQb->getSQL()
            .' ON DUPLICATE KEY UPDATE reviews_count = VALUES(reviews_count) ',
            $sqParams,
            $sqParamsTypes
        );
    }

    public function updateDemandsCounter(UpdateStatsSpec $updateStatsSpec)
    {
        $table = $this->getClassMetadata()->getTableName();
        $connection = $this->_em->getConnection();

        if ($updateStatsSpec->isReset()) {
            $resetQb = $connection->createQueryBuilder()
                ->update($table, 'sd')
                ->set('demands_count', 0)
                ->set('demands_processed_count', 0);

            if ($updateStatsSpec->companiesIds) {
                $resetQb
                    ->andWhere('sd.company_id IN (:companies_ids)')
                    ->setParameter('companies_ids', $updateStatsSpec->companiesIds, Connection::PARAM_INT_ARRAY);
            }
            $resetQb->execute();
        }

        $sqParams = $sqParamsTypes = array();

        $sqQb = $connection->createQueryBuilder()
            ->select('d.company_id')
            ->addSelect('COUNT(d.id) AS demands_count')
            ->addSelect('COUNT(d.viewed_at) AS demands_processed_count')
            ->from('demand', 'd')
            ->andWhere('d.demand_type = :demands_type')
            ->groupBy('d.company_id');

        $sqParams['demands_type'] = Demand::TYPE_PRIVATE;
        $sqParamsTypes['demands_type'] = \PDO::PARAM_INT;

        switch ($table) {
            case ClientStats::GROUP_BY_DAILY_TABLE:
                $column = 'date';
                $sqQb
                    ->addSelect('DATE(d.created_at) AS _date')
                    ->addGroupBy('_date');
                break;

            case ClientStats::GROUP_BY_CITY_TABLE:
                $column = 'city_id, date';
                $sqQb
                    ->addSelect('d.city_id')
                    ->addSelect('DATE(d.created_at) AS _date')
                    ->addGroupBy('_date')
                    ->addGroupBy('d.city_id');
                break;

            case ClientStats::GROUP_BY_CATEGORY_TABLE:
                $column = 'category_id, date, aggregation_column, source_type_id';
                $sqQb
                    ->addSelect('IF(d.category_id, d.category_id, NULL)')
                    ->addSelect('DATE(d.created_at) AS _date')
                    ->addSelect('IFNULL(d.category_id, d.source_type * -1) AS _ac')
                    ->addSelect('IF(d.category_id IS NULL, d.source_type, NULL)')
                    ->addGroupBy('_date')
                    ->addGroupBy('_ac');
                break;

            default:
                throw new \InvalidArgumentException('Unknown grouping');
        }

        if (null !== $updateStatsSpec->dateStart) {
            $sqQb->andWhere('d.created_at >= :date_from');
            $sqParams['date_from'] = $updateStatsSpec->dateStart;
            $sqParamsTypes['date_from'] = 'date';
        }

        if ($updateStatsSpec->companiesIds) {
            $sqQb->andWhere('d.company_id IN (:companies_ids)');
            $sqParams['companies_ids'] = $updateStatsSpec->companiesIds;
            $sqParamsTypes['companies_ids'] = Connection::PARAM_INT_ARRAY;

        }

        $connection->executeUpdate(
            'INSERT INTO '.$table.' (company_id, demands_count, demands_processed_count, '.$column.') '.$sqQb->getSQL()
            .' ON DUPLICATE KEY UPDATE demands_count = VALUES(demands_count),
             demands_processed_count = VALUES(demands_processed_count) ',
            $sqParams,
            $sqParamsTypes
        );
    }

    public function updateDemandsViewsCounter(UpdateStatsSpec $updateStatsSpec)
    {
        $table = $this->getClassMetadata()->getTableName();
        $connection = $this->_em->getConnection();

        if ($updateStatsSpec->isReset()) {
            $resetQb = $connection->createQueryBuilder()
                ->update($table, 'sd')
                ->set('demands_views_count', 0);

            if ($updateStatsSpec->companiesIds) {
                $resetQb
                    ->andWhere('sd.company_id IN (:companies_ids)')
                    ->setParameter('companies_ids', $updateStatsSpec->companiesIds, Connection::PARAM_INT_ARRAY);
            }
            $resetQb->execute();
        }

        $sqParams = $sqParamsTypes = array();

        $sqQb = $connection->createQueryBuilder()
            ->select('u.ConnectCompany')
            ->addSelect('COUNT(dv.demand_id) AS demands_count')
            ->from('demand_view', 'dv')
            ->join('dv', 'User', 'u', 'dv.user_id = u.User_ID')
            ->join('dv', 'demand', 'd', 'dv.demand_id = d.id')
            ->andWhere('d.demand_type = :demands_type')
            ->addGroupBy('u.ConnectCompany');

        $sqParams['demands_type'] = Demand::TYPE_PUBLIC;
        $sqParamsTypes['demands_type'] = \PDO::PARAM_INT;

        switch ($table) {
            case ClientStats::GROUP_BY_DAILY_TABLE:
                $column = 'date';
                $sqQb
                    ->addSelect('DATE(dv.viewed_at) AS _date')
                    ->addGroupBy('_date');
                break;

            case ClientStats::GROUP_BY_CITY_TABLE:
                $column = 'city_id, date';
                $sqQb
                    ->addSelect('d.city_id')
                    ->addSelect('DATE(dv.viewed_at) AS _date')
                    ->addGroupBy('_date')
                    ->addGroupBy('d.city_id');
                break;

            case ClientStats::GROUP_BY_CATEGORY_TABLE:
                $column = 'category_id, date, aggregation_column, source_type_id';
                $sqQb
                    ->addSelect('IF(d.category_id, d.category_id, NULL)')
                    ->addSelect('DATE(dv.viewed_at) AS _date')
                    ->addSelect('IFNULL(d.category_id, d.source_type * -1) AS _ac')
                    ->addSelect('IF(d.category_id IS NULL, d.source_type, NULL)')
                    ->addGroupBy('_date')
                    ->addGroupBy('_ac');
                break;

            default:
                throw new \InvalidArgumentException('Unknown grouping');
        }

        if (null !== $updateStatsSpec->dateStart) {
            $sqQb->andWhere('dv.viewed_at >= :date_from');
            $sqParams['date_from'] = $updateStatsSpec->dateStart;
            $sqParamsTypes['date_from'] = 'date';
        }

        if ($updateStatsSpec->companiesIds) {
            $sqQb->andWhere('u.ConnectCompany IN (:companies_ids)');
            $sqParams['companies_ids'] = $updateStatsSpec->companiesIds;
            $sqParamsTypes['companies_ids'] = Connection::PARAM_INT_ARRAY;

        }

        $connection->executeUpdate(
            'INSERT INTO '.$table.' (company_id, demands_views_count, '.$column.') '.$sqQb->getSQL()
            .' ON DUPLICATE KEY UPDATE demands_views_count = VALUES(demands_views_count) ',
            $sqParams,
            $sqParamsTypes
        );
    }

    public function updateDemandsToFavoriteCounter(UpdateStatsSpec $updateStatsSpec)
    {
        $table = $this->getClassMetadata()->getTableName();
        $connection = $this->_em->getConnection();

        if ($updateStatsSpec->isReset()) {
            $resetQb = $connection->createQueryBuilder()
                ->update($table, 'sd')
                ->set('demands_to_favorite_count', 0);

            if ($updateStatsSpec->companiesIds) {
                $resetQb
                    ->andWhere('sd.company_id IN (:companies_ids)')
                    ->setParameter('companies_ids', $updateStatsSpec->companiesIds, Connection::PARAM_INT_ARRAY);
            }
            $resetQb->execute();
        }

        $sqParams = $sqParamsTypes = array();

        $sqQb = $connection->createQueryBuilder()
            ->select('u.ConnectCompany')
            ->addSelect('COUNT(f.demand_id) AS demands_count')
            ->from('favorite', 'f')
            ->join('f', 'User', 'u', 'f.user_id = u.User_ID')
            ->addGroupBy('u.ConnectCompany');

        switch ($table) {
            case ClientStats::GROUP_BY_DAILY_TABLE:
                $column = 'date';
                $sqQb
                    ->addSelect('DATE(f.created_at) AS _date')
                    ->addGroupBy('_date');
                break;

            case ClientStats::GROUP_BY_CITY_TABLE:
                $column = 'city_id, date';
                $sqQb
                    ->addSelect('d.city_id')
                    ->addSelect('DATE(f.created_at) AS _date')
                    ->join('f', 'demand', 'd', 'f.demand_id = d.id')
                    ->addGroupBy('_date')
                    ->addGroupBy('d.city_id');
                break;

            case ClientStats::GROUP_BY_CATEGORY_TABLE:
                $column = 'category_id, date, aggregation_column, source_type_id';
                $sqQb
                    ->addSelect('IF(d.category_id, d.category_id, NULL)')
                    ->addSelect('DATE(f.created_at) AS _date')
                    ->addSelect('IFNULL(d.category_id, d.source_type * -1) AS _ac')
                    ->addSelect('IF(d.category_id IS NULL, d.source_type, NULL)')
                    ->join('f', 'demand', 'd', 'f.demand_id = d.id')
                    ->addGroupBy('_date')
                    ->addGroupBy('_ac');
                break;

            default:
                throw new \InvalidArgumentException('Unknown grouping');
        }

        if (null !== $updateStatsSpec->dateStart) {
            $sqQb->andWhere('f.created_at >= :date_from');
            $sqParams['date_from'] = $updateStatsSpec->dateStart;
            $sqParamsTypes['date_from'] = 'date';
        }

        if ($updateStatsSpec->companiesIds) {
            $sqQb->andWhere('u.ConnectCompany IN (:companies_ids)');
            $sqParams['companies_ids'] = $updateStatsSpec->companiesIds;
            $sqParamsTypes['companies_ids'] = Connection::PARAM_INT_ARRAY;

        }

        $connection->executeUpdate(
            'INSERT INTO '.$table.' (company_id, demands_to_favorite_count, '.$column.') '.$sqQb->getSQL()
            .' ON DUPLICATE KEY UPDATE demands_to_favorite_count = VALUES(demands_to_favorite_count) ',
            $sqParams,
            $sqParamsTypes
        );
    }

    public function updateDemandsAnswersCounter(UpdateStatsSpec $updateStatsSpec)
    {
        $table = $this->getClassMetadata()->getTableName();
        $connection = $this->_em->getConnection();

        if ($updateStatsSpec->isReset()) {
            $resetQb = $connection->createQueryBuilder()
                ->update($table, 'sd')
                ->set('demands_answers_count', 0);

            if ($updateStatsSpec->companiesIds) {
                $resetQb
                    ->andWhere('sd.company_id IN (:companies_ids)')
                    ->setParameter('companies_ids', $updateStatsSpec->companiesIds, Connection::PARAM_INT_ARRAY);
            }
            $resetQb->execute();
        }

        $sqParams = $sqParamsTypes = array();

        $sqQb = $connection->createQueryBuilder()
            ->select('u.ConnectCompany')
            ->addSelect('COUNT(da.demand_id) AS demands_count')
            ->from('demand_answer', 'da')
            ->join('da', 'User', 'u', 'da.user_id = u.User_ID')
            ->addGroupBy('u.ConnectCompany');

        switch ($table) {
            case ClientStats::GROUP_BY_DAILY_TABLE:
                $column = 'date';
                $sqQb
                    ->addSelect('DATE(da.created_at) AS _date')
                    ->addGroupBy('_date');
                break;

            case ClientStats::GROUP_BY_CITY_TABLE:
                $column = 'city_id, date';
                $sqQb
                    ->addSelect('d.city_id')
                    ->addSelect('DATE(da.created_at) AS _date')
                    ->join('da', 'demand', 'd', 'da.demand_id = d.id')
                    ->addGroupBy('_date')
                    ->addGroupBy('d.city_id');
                break;

            case ClientStats::GROUP_BY_CATEGORY_TABLE:
                $column = 'category_id, date, aggregation_column, source_type_id';
                $sqQb
                    ->addSelect('IF(d.category_id, d.category_id, NULL)')
                    ->addSelect('DATE(da.created_at) AS _date')
                    ->addSelect('IFNULL(d.category_id, d.source_type * -1) AS _ac')
                    ->addSelect('IF(d.category_id IS NULL, d.source_type, NULL)')
                    ->join('da', 'demand', 'd', 'da.demand_id = d.id')
                    ->addGroupBy('_date')
                    ->addGroupBy('_ac');
                break;



            default:
                throw new \InvalidArgumentException('Unknown grouping');
        }

        if (null !== $updateStatsSpec->dateStart) {
            $sqQb->andWhere('da.created_at >= :date_from');
            $sqParams['date_from'] = $updateStatsSpec->dateStart;
            $sqParamsTypes['date_from'] = 'date';
        }

        if ($updateStatsSpec->companiesIds) {
            $sqQb->andWhere('u.ConnectCompany IN (:companies_ids)');
            $sqParams['companies_ids'] = $updateStatsSpec->companiesIds;
            $sqParamsTypes['companies_ids'] = Connection::PARAM_INT_ARRAY;

        }

        $connection->executeUpdate(
            'INSERT INTO '.$table.' (company_id, demands_answers_count, '.$column.') '.$sqQb->getSQL()
            .' ON DUPLICATE KEY UPDATE demands_answers_count = VALUES(demands_answers_count) ',
            $sqParams,
            $sqParamsTypes
        );
    }

    protected function processStatsResultQuery(QueryBuilder $qb, array $criteria)
    {
        if (!empty($criteria['company'])) {
            $qb
                ->leftJoin($this->_entityName, 'stats', 'WITH', 'd.date = stats.date AND stats.company = :company')
                ->setParameter('company', $criteria['company']);
        } else {
            $qb->leftJoin($this->_entityName, 'stats', 'WITH', 'd.date = stats.date');
        }
    }
}
