<?php

namespace Metal\StatisticBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\ProjectBundle\Helper\FormattingHelper;
use Metal\StatisticBundle\Entity\DTO\StatsResultAbstract;

class DefaultHelper extends HelperAbstract
{
    public function canCreateFakeStatsElement()
    {
        $multiplyStatistics = $this->container->getParameter('multiply_statistics');

        foreach ($multiplyStatistics as $multiplyStatistic) {
            $now = new \DateTime('now');
            $dateFrom = new \DateTime($multiplyStatistic['date_from']);
            $dateTo = new \DateTime($multiplyStatistic['date_to']);
            $coefficient = $multiplyStatistic['coefficient'];

            if ($now >= $dateFrom && $now <= $dateTo && mt_rand(1, 100) <= ($coefficient - 1) * 100) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param StatsResultAbstract[] $stats
     * @param array $chartRules
     *
     * @return array
     */
    public function getSeriesByRules($stats, array $chartRules)
    {
        $data = array();

        foreach ($chartRules['series'] as $rule) {
            $points = array();
            foreach ($stats as $stat) {
                $field = $rule['field'];
                $points[] = $stat->$field;
            }
            $data[] = array(
                'name' => $rule['title'],
                'data' => $points,
                'type' => 'spline',
                'marker' => array(
                    'symbol' => 'circle'
                )
            );
        }

        return $data;
    }

    /**
     * @param StatsResultAbstract[] $stats
     * @param $grouping
     *
     * @return array
     */
    public function getDateAxis($stats, $grouping)
    {
        $formattingHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Formatting');
        /* @var $formattingHelper FormattingHelper */
        $dateAxis = array();

        switch ($grouping) {
            case 'day':
                foreach ($stats as $stat) {
                    $key = $formattingHelper->formatDate($stat->date, 'full');
                    $dateAxis[$key] = $formattingHelper->formatStatsDate($stat->date, $grouping, $stat->previousEntry ? $stat->previousEntry->date : null);
                }

            break;

            case 'week':
                foreach ($stats as $stat) {
                    $dateStart = clone $stat->date;
                    $dateStart->modify('this week monday');
                    $dateEnd = clone $stat->date;
                    $dateEnd->modify('this week sunday');
                    $key = 'с '.$formattingHelper->formatDate($dateStart, 'full').' по '.$formattingHelper->formatDate($dateEnd, 'full');
                    $dateAxis[$key] = $formattingHelper->formatStatsDate($stat->date, $grouping, $stat->previousEntry ? $stat->previousEntry->date : null);
                }
            break;

            case 'month':
                foreach ($stats as $stat) {
                    $key = $formattingHelper->formatYearMonth($stat->date, 'normal');
                    $dateAxis[$key] = $formattingHelper->formatStatsDate($stat->date, $grouping, $stat->previousEntry ? $stat->previousEntry->date : null);
                }
            break;
        }

        return $dateAxis;
    }

    /**
     * @param StatsResultAbstract[] $stats
     * @param array $chartRules
     * @param $labelField
     *
     * @return array
     */
    public function getPieData($stats, array $chartRules, $labelField)
    {
        $data = array();

        $position = 350;
        if (count($chartRules['fields']) > 1) {
            $position = 200;
        }

        foreach ($chartRules['fields'] as $key => $fieldName) {
            $fields = array();
            foreach ($stats as $stat) {
                $fields[] = array($stat->$labelField, $stat->$fieldName);
//                $fields[] = array($stat->$labelField, mt_rand(0, 20));
            }

            $data[] = array(
                'type' => 'pie',
                'name' => $chartRules['titles'][$key],
                'title' => array(
                    'text' => $chartRules['titles'][$key],
                    'verticalAlign' => 'top',
                    'y' => -30,
                    'align' => 'center',
                    'x' => -55
                ),
                'data' => $fields,
                'size' => 180,
                'center' => array($position, 98),
            );

            $position += 250;
        }

        return $data;
    }

    /**
     * @param array $stats
     *
     * @return array
     */
    public function getTotalStats(array $stats)
    {
        $totalStats = array();
        foreach ($stats as $stat) {
            foreach ($stat as $counterName => $count) {
                if (!isset($totalStats[$counterName])) {
                    $totalStats[$counterName] = $count;
                } else {
                    $totalStats[$counterName] += $count;
                }
            }
        }

        return $totalStats;
    }

    /**
     * @param array $stats
     *
     * @return array
     */
    public function getSumTotalStats(array $stats)
    {
        return array_sum($this->getTotalStats($stats));
    }

    /**
     * @param array $stats
     *
     * @return array
     */
    public function getTotalStatsByMonth(array $stats)
    {
        $totalStatsByMonth = array();
        foreach ($stats as $monthId => $stat) {
            $totalStatsByMonth[$monthId] = array_sum($stat);
        }

        return $totalStatsByMonth;
    }
}
