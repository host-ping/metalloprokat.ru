<?php

namespace Metal\DemandsBundle\Notifications\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\DemandsBundle\Notifications\Model\DemandSubscriptionInfo;
use Metal\TerritorialBundle\Entity\TerritorialStructure;

class DemandSubscriptionInfoProvider
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getDemandSubscriptionInfo(int $demandId): DemandSubscriptionInfo
    {
        $demand = $this->em->getRepository(AbstractDemand::class)->find($demandId);

        $categoryIds = $demand->getCategoriesIdsWithAllParents();
        $territorialStructureIds = $this->getTerritorialStructureIds($demand);

        return new DemandSubscriptionInfo($categoryIds, $territorialStructureIds);
    }

    private function getTerritorialStructureIds(AbstractDemand $demand): array
    {
        $territorialStructureRepository = $this->em->getRepository(TerritorialStructure::class);
        $territorialStructureIds = [];

        if ($demand->getCity()) {
            $cityId = $demand->getCity()->getId();
            $territorialIdsPerCities = $territorialStructureRepository->getTerritorialIdsPerCities();
            //FIXME: ошибки с заявкой №848452 Фергана, №848415 Астана
            $territorialStructureIds[] = $territorialIdsPerCities[$cityId] ?? null;
        }

        if ($demand->getRegion()) {
            $regionId = $demand->getRegion()->getId();
            $territorialIdsPerRegions = $territorialStructureRepository->getTerritorialIdsPerRegions();
            $territorialStructureIds[] = $territorialIdsPerRegions[$regionId] ?? null;

            $federalDistrictId = $demand->getRegion()->getFederalDistrict()->getId();
            $territorialIdsPerFederalDistrict = $territorialStructureRepository->getTerritorialIdsPerFederalDistrict();
            $territorialStructureIds[] = $territorialIdsPerFederalDistrict[$federalDistrictId] ?? null;
        }

        return array_values(array_filter($territorialStructureIds));
    }
}
