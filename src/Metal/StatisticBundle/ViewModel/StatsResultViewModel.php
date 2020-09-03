<?php

namespace Metal\StatisticBundle\ViewModel;

use Metal\StatisticBundle\Entity\DTO\StatsResultAbstract;

class StatsResultViewModel
{
    /**
     * READONLY. Use setter for setting value to this property.
     *
     * @var StatsResultAbstract[]
     */
    public $stats = array();

    /**
     * @var \DateTime
     */
    public $dateFrom;

    /**
     * @var \DateTime
     */
    public $dateTo;

    public function __construct(array $stats, \DateTime $dateFrom, \DateTime $dateTo, $linkEntries = false, $removeEmpty = null)
    {
        $this->setStats($stats, $linkEntries, null === $removeEmpty ? !$linkEntries : $removeEmpty);
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function getSum(array $fields)
    {
        $sum = array_fill_keys($fields, 0);
        foreach ($this->stats as $statItem) {
            foreach ($fields as $field) {
                $sum[$field] += $statItem->$field;
            }
        }

        return $sum;
    }

    /**
     * @param StatsResultAbstract[] $stats
     * @param bool $linkEntries
     * @param bool $removeEmpty
     */
    public function setStats(array $stats, $linkEntries, $removeEmpty)
    {
        if ($removeEmpty) {
            $stats = array_values(array_filter($stats, function (StatsResultAbstract $statsEntry) {
                    return $statsEntry->hasData;
                }
            ));
        }

        if ($linkEntries) {
            for ($i = 1, $n = count($stats); $i < $n; $i++) {
                $statsItem = $stats[$i];
                $statsItem->previousEntry = $stats[$i - 1];
            }
        }

        $this->stats = $stats;
    }
}
