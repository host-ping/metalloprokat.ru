<?php

namespace Metal\ServicesBundle\Helper;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\BufferedOutput;

class DefaultHelper
{
    public function prepareTextTable(array $packages, array $periods)
    {
        $newBuffer = new BufferedOutput();
        $table = new Table($newBuffer);

        $packagesTitlesRow = array('');
        foreach ($packages as $package) {
            $packagesTitlesRow[] = $package->getTitle();
        }

        $packagesPeriodsWithPriceRows = array();
        foreach ($periods as $periodId => $period) {
            $packagesPeriodsWithPriceRows[$periodId][] = $period;
            foreach ($packages as $package) {
                $packagesPeriodsWithPriceRows[$periodId][] = $package->getPriceByPeriod($periodId) ?: '—';
            }
        }

        $table->addRow($packagesTitlesRow);
        $table->addRows($packagesPeriodsWithPriceRows);

        $table->render();

        return $newBuffer->fetch();
    }
}
