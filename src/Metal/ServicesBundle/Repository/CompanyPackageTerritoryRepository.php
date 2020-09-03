<?php

namespace Metal\ServicesBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Metal\ServicesBundle\Entity\CompanyPackageTerritory;

class CompanyPackageTerritoryRepository extends EntityRepository
{
    public function replaceCodeAccessToPriorityByPackage(array $companiesWithCodeAccess, $priorityShowPackages)
    {
        if (!$companiesWithCodeAccess) {
            return array();
        }

        $companiesWithPriority = array();
        foreach ($companiesWithCodeAccess as $companyId => $codesAccessByTerritories) {
            if (isset($codesAccessByTerritories['regions'])) {
                foreach ($codesAccessByTerritories['regions'] as $regionId => $codeAccess) {
                    $companiesWithPriority[$companyId]['regions'][$regionId] = $priorityShowPackages[$codeAccess];
                }
            }

            if (isset($codesAccessByTerritories['cities'])) {
                foreach ($codesAccessByTerritories['cities'] as $cityId => $codeAccess) {
                    $companiesWithPriority[$companyId]['cities'][$cityId] = $priorityShowPackages[$codeAccess];
                }
            }
        }

        return $companiesWithPriority;
    }

    public function resetTerritorialCodeAccess(array $companiesIds = array(), $forAll = false)
    {
        if (!$forAll && !$companiesIds) {
            return;
        }

        $parameters = array();
        $types = array();
        if ($companiesIds) {
            $parameters['companies_ids'] = $companiesIds;
            $types['companies_ids'] = Connection::PARAM_INT_ARRAY;
        }

        $this->_em->getConnection()
            ->executeUpdate(
                sprintf(
                    '
                    UPDATE Message75 c
                    SET c.code_access_territory = null
                    %s',
                    $companiesIds ? 'WHERE c.Message_ID IN (:companies_ids)' : ''
                ),
                $parameters,
                $types
            );
    }

    public function getActualPackagesByCompanies(array $companiesIds, $actual = true)
    {
        if (!count($companiesIds)) {
            return array();
        }

        $qb = $this->createQueryBuilder('cpt')
            ->select('cpt')
            ->where('cpt.company IN (:companies_ids)')
            ->setParameter('companies_ids', $companiesIds);

        if ($actual) {
            $qb
                ->andWhere(':now BETWEEN cpt.startsAt AND cpt.endsAt')
                ->setParameter('now', new \DateTime());
        }

        return $qb->getQuery()->getResult();
    }

    public function getPackagesByTerritories(array $companiesToPackages, $actual = true)
    {
        $packages = $this->getActualPackagesByCompanies($companiesToPackages, $actual);
        /* @var $packages CompanyPackageTerritory[] */
        if (!count($packages)) {
            return array();
        }

        $citiesByRegions = $this->_em->getRepository('MetalTerritorialBundle:Region')->getCitiesByRegions();

        $companiesPackageByTerritory = array();
        foreach ($packages as $package) {
            $companyId = $package->getCompany()->getId();
            if ($region = $package->getTerritory()->getRegion()) {
                $regionId = $region->getId();
                $companiesPackageByTerritory[$companyId]['regions'][$regionId] = $package->getPackageId();
                if (isset($citiesByRegions[$regionId])) {
                    foreach ($citiesByRegions[$regionId] as $cityId) {
                        $companiesPackageByTerritory[$companyId]['cities'][$cityId] = $package->getPackageId();
                    }
                }
            } elseif ($city = $package->getTerritory()->getCity()) {
                $companiesPackageByTerritory[$companyId]['cities'][$city->getId()] = $package->getPackageId();
            }
        }

        return $companiesPackageByTerritory;
    }
}
