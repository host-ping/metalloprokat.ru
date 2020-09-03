<?php

namespace Metal\StatisticBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use Metal\AnnouncementsBundle\Entity\Announcement;
use Metal\AnnouncementsBundle\Entity\StatsElement;
use Metal\ProjectBundle\Doctrine\Utils;
use Metal\ProjectBundle\Util\InsertUtil;
use Metal\StatisticBundle\DataFetching\UpdateStatsSpec;

class StatsAnnouncementRepository extends AbstractStatsRepository
{
    public function truncate()
    {
        $table = $this->getClassMetadata()->getTableName();
        $connection = $this->_em->getConnection();

        $connection->executeUpdate('TRUNCATE '.$table);
    }

    public function resetStatsAnnouncementCount(UpdateStatsSpec $updateStatsSpec)
    {
        $connection = $this->_em->getConnection();
        Utils::checkConnection($connection);


    }


    protected function processStatsResultQuery(QueryBuilder $qb, array $criteria)
    {
        if (!empty($criteria['announcement'])) {
            $qb
                ->leftJoin($this->_entityName, 'stats', 'WITH', 'd.date = stats.date AND stats.announcement = :announcament')
                ->setParameter('announcament', $criteria['announcement']);
        }
    }

    public function getStatsAnnouncementDateRange(Connection $connection = null)
    {
        if (null === $connection) {
            $connection = $this->_em->getConnection();
        }

        return $connection->fetchAssoc('SELECT MIN(ase.date_created_at) as _min, MAX(ase.date_created_at) as _max FROM announcement_stats_element AS ase');
    }

    public function getDateRangeForAnnouncement(Announcement $announcement)
    {
        $dateRange = $this
            ->createQueryBuilder('sad')
            ->select('MIN(sad.date) AS _min')
            ->addSelect('MAX(sad.date) AS _max')
            ->where('sad.announcement = :announcement')
            ->setParameter('announcement', $announcement)
            ->getQuery()
            ->getSingleResult();

        if ($dateRange['_min'] && $dateRange['_max']) {
            return array(new \DateTime($dateRange['_min']), new \DateTime($dateRange['_max']));
        }

        return array($announcement->getStartsAt(), $announcement->getEndsAt());
    }

    public function updateStatsAnnouncementCount(UpdateStatsSpec $updateStatsSpec, Connection $conn = null)
    {
        $connWrite = $this->_em->getConnection();
        $conn = $conn ?: $connWrite;

        if ($updateStatsSpec->isReset()) {
            Utils::checkConnection($connWrite);
            $connWrite
                ->createQueryBuilder()
                ->update($this->getClassMetadata()->getTableName(), 'sa')
                ->set('redirects_count', 0)
                ->set('displays_count', 0)
                ->where('sa.date >= :from_date')
                ->setParameter('from_date', $updateStatsSpec->dateFrom, 'date')
                ->andWhere('sa.date <= :to_date')
                ->setParameter('to_date', $updateStatsSpec->dateTo, 'date')
                ->execute();
        }

        Utils::checkConnection($conn);

        $data = $conn->createQueryBuilder()
            ->select('ase.announcement_id AS announcement_id')
            ->addSelect('COUNT(IF(ase.action = :redirect, 1, NULL)) AS redirects_count')
            ->setParameter('redirect', StatsElement::ACTION_REDIRECT, \PDO::PARAM_INT)
            ->addSelect('COUNT(IF(ase.action = :display, 1, NULL)) AS displays_count')
            ->setParameter('display', StatsElement::ACTION_VIEW, \PDO::PARAM_INT)
            ->addSelect('ase.date_created_at AS date')
            ->from('announcement_stats_element', 'ase')
            ->where('ase.date_created_at >= :from_date')
            ->setParameter('from_date', $updateStatsSpec->dateFrom, 'date')
            ->andWhere('ase.date_created_at <= :to_date')
            ->setParameter('to_date', $updateStatsSpec->dateTo, 'date')
            ->groupBy('ase.announcement_id')
            ->addGroupBy('ase.date_created_at')
            ->execute()
            ->fetchAll();

        Utils::checkConnection($connWrite);

        InsertUtil::insertMultipleOrUpdate(
            $connWrite,
            $this->getClassMetadata()->getTableName(),
            $data,
            array('redirects_count', 'displays_count'),
            1000
        );

        return count($data);
    }
}
