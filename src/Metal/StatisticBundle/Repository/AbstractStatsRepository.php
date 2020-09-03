<?php

namespace Metal\StatisticBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Metal\StatisticBundle\Entity\DTO\StatsResultAbstract;

abstract class AbstractStatsRepository extends EntityRepository
{
    protected function processStatsResultQuery(QueryBuilder $qb, array $criteria)
    {
        // you can override if required
    }

    /**
     * @param StatsResultAbstract $dto
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @param array $criteria
     * @param string $group
     * @param string $order
     * @param string $direction
     *
     * @return StatsResultAbstract[]
     */
    public function getStatsResults(StatsResultAbstract $dto, \DateTime $dateFrom, \DateTime $dateTo, array $criteria = array(), $group = null, $order = null, $direction = null)
    {
        $group = $group ?: $dto->getDefaultGrouping();
        $defaultOrder = $dto->getDefaultOrder();
        $order = $order ?: $defaultOrder;
        $direction = $direction ?: $dto->getDefaultOrderDirection($order);

        $statsQb = $this
            ->_em
            ->createQueryBuilder()
            ->from('MetalStatisticBundle:Day', 'd')
            ->andWhere('d.date >= :date_from AND d.date <= :date_to')
            ->setParameter('date_from', $dateFrom, 'date')
            ->setParameter('date_to', $dateTo, 'date');

        $this->processStatsResultQuery($statsQb, $criteria);

        $columns = array();
        $availableOrders = array();
        foreach ($dto->getFields() as $field => $fieldOptions) {
            $dqlField = isset($fieldOptions['readFrom']) ? $fieldOptions['readFrom'] : sprintf('stats.%s', $field);

            if ($group !== 'day') {
                $groupFunction = isset($fieldOptions['groupFunction']) ? $fieldOptions['groupFunction'] : 'SUM';
                if ($groupFunction) {
                    $dqlField = sprintf('%s(%s)', $groupFunction, $dqlField);
                }
            }

            $columns[$field] = $dqlField;
            $availableOrders[$field] = $dqlField;
        }

        if (!isset($availableOrders[$order])) {
            throw new \InvalidArgumentException('Unknown order.');
        }

        if (null !== $group) {
            switch ($group) {
                case 'day':
                    break;

                case 'week':
                    $statsQb->addGroupBy('d.yearWeek');
                    break;

                case 'month':
                    $statsQb->addGroupBy('d.yearMonth');
                    break;

                default:
                    throw new \InvalidArgumentException('Unknown grouping.');
            }
        }

        $statsQb->select(sprintf('NEW %s(%s)', get_class($dto), implode(', ', $columns)));

        $statsQb
            ->addSelect($availableOrders[$order].' AS HIDDEN orderField')
            ->orderBy('orderField', $direction);

        if ($order !== $defaultOrder) {
            $statsQb
                ->addSelect($availableOrders[$defaultOrder].' AS HIDDEN additionalOrderField')
                ->addOrderBy('additionalOrderField', $dto->getDefaultOrderDirection($defaultOrder));
        }

        return $statsQb->getQuery()->getResult();
    }
}
