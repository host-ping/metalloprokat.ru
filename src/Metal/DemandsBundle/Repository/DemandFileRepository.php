<?php

namespace Metal\DemandsBundle\Repository;

use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\ProjectBundle\Doctrine\EntityRepository;

class DemandFileRepository extends EntityRepository
{
    /**
     * @param AbstractDemand[] $demands
     */
    public function attachDemandFiles(array $demands)
    {
        if (!count($demands)) {
            return;
        }

        $directedDemands = array();
        foreach ($demands as $demand) {
            $demand->setAttribute('demandFiles', array());

            $directedDemands[$demand->getId()] = $demand;
        }

        $demandsFiles = $this->createQueryBuilder('df')
            ->select('df AS demandFile, IDENTITY(df.demand) AS demandId')
            ->andWhere('df.demand IN (:demands_ids)')
            ->setParameter('demands_ids', array_keys($directedDemands))
            ->getQuery()
            ->getResult();

        $demandFilesPerDemand = array();
        foreach ($demandsFiles as $demandsFile) {
            $id = $demandsFile['demandId'];
            if (!isset($demandFilesPerDemand[$id])) {
                $demandFilesPerDemand[$id] = array();
            }

            $demandFilesPerDemand[$id][] = $demandsFile['demandFile'];
        }

        foreach ($demandFilesPerDemand as $id => $demandsFiles) {
            $directedDemands[$id]
                ->setAttribute('demandFiles', $demandsFiles);
        }
    }
}
