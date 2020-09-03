<?php

namespace Metal\DemandsBundle\Indexer;

use Brouzie\Sphinxy\Indexer\DoctrineQbIndexer;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\DemandsBundle\Entity\ValueObject\DemandPeriodicityProvider;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\TerritorialStructure;

class DemandsIndexer extends DoctrineQbIndexer
{
    public function processItems(array $items)
    {
        $demandsToCategories = array();
        $demandsToCategoriesTitle = array();
        $demandsToAttributesIds = array();

        $territorialStructureRepository = $this->em->getRepository(TerritorialStructure::class);
        $resultCitiesIds = $territorialStructureRepository->getTerritorialIdsPerCities();
        $resultRegionsIds = $territorialStructureRepository->getTerritorialIdsPerRegions();
        $resultDistrictIds = $territorialStructureRepository->getTerritorialIdsPerFederalDistrict();

        // получаем все города заявок
        foreach ($items as $demandRow) {
            $demandId = $demandRow['demand']['id'];
            $demandsToCategories[$demandId] = array();
            $demandsToAttributesIds[$demandId] = array();
        }

        $demandCategories = $this->em->createQueryBuilder()
            ->select('IDENTITY(dc.demand) AS demandId, c.title, c.branchIds, c.virtualParentsIds')
            ->from('MetalDemandsBundle:DemandCategory', 'dc')
            ->join('dc.category', 'c')
            ->where('dc.demand IN (:ids)')
            ->setParameter('ids', array_keys($demandsToCategories))
            ->getQuery()
            ->getResult()
        ;

        foreach ($demandCategories as $demandCategory) {
            $demandId = $demandCategory['demandId'];
            $demandsToCategories[$demandId] = array_unique(
                array_merge(
                    $demandsToCategories[$demandId],
                    explode(',', $demandCategory['branchIds']),
                    explode(',', $demandCategory['virtualParentsIds'])
                )
            );
            $demandsToCategoriesTitle[$demandId][] = $demandCategory['title'];
        }

        $demandsToAttributes = $this->em->getRepository('MetalDemandsBundle:DemandItemAttributeValue')
            ->getDemandsAttributes(array_keys($demandsToAttributesIds));

        foreach ($demandsToAttributes as $demandId => $demandToAttribute) {
            foreach ((array)$demandToAttribute as $attribute) {
                foreach ((array)$attribute as $item) {
                    $demandsToAttributesIds[$demandId] = array_unique(
                        array_merge($demandsToAttributesIds[$demandId], $item)
                    );
                }
            }
        }

        foreach ($items as $i => $demandRow) {
            $demand = $demandRow['demand'];

            $demandId = $demand['id'];
            $titleArray = array();
            foreach ($demand['demandItems'] as $item) {
                $titleArray[] = $item['title'].' '.$item['size'];
            }
            $title = implode(' ', $titleArray).' ';

            if (!empty($demandsToCategoriesTitle[$demandId])) {
                foreach ($demandsToCategoriesTitle[$demandId] as $demandCategoryRow) {
                    $title .= ' '.$demandCategoryRow;
                }
            }

            $cityId =  $demandRow['citySlug'] ? $demandRow['cityId'] : $demandRow['administrativeCenterId'];

            $territorialStructureIds = array();
            if (isset($resultCitiesIds[$cityId], $resultRegionsIds[$demandRow['regionId']])) {
                $territorialStructureIds = array(
                    $resultCitiesIds[$cityId],
                    $resultRegionsIds[$demandRow['regionId']],
                );
            }

            if (isset($resultDistrictIds[$demandRow['federalDistrictId']])) {
                $territorialStructureIds[] = $resultDistrictIds[$demandRow['federalDistrictId']];
            }

            $title = AttributeValue::normalizeTitle($title);

            $items[$i] = array(
                'id' => $demandId,
                'title' => $title,
                'demand_title' => $title,
                //'demand_title' => ' ',
                'attributes_ids' => $demandsToAttributesIds[$demandId],
                'attributes' => json_encode(isset($demandsToAttributes[$demandId]) ? $demandsToAttributes[$demandId] : array()),
                'category_id' => $demandRow['categoryId'],
                'categories_ids' => array_filter($demandsToCategories[$demandId]),
                'city_id' => $cityId,
                'region_id' => $demandRow['regionId'],
                'territorial_structure_ids' => $territorialStructureIds,
                //TODO: delete-after-merge-facets
                'country_id' => $demandRow['countryId'],
                'countries_ids' => $this->prepareCountriesIds($demandRow['countryId']),
                'created_at' => max($demand['createdAt']->getTimestamp(), $demand['moderatedAt']->getTimestamp()),

                'demand_views_count' => $demandRow['viewsCount'],
                'demand_answers_count' => $demandRow['answersCount'],

                'is_wholesale' => (boolean)$demand['wholesale'],
                'is_repetitive' => $demand['demandPeriodicityId'] != DemandPeriodicityProvider::ONCE,
                'author_type' => $demand['consumerTypeId'],
            );
        }

        return $items;
    }

    protected function getQueryBuilder()
    {
        return $this->em
            ->createQueryBuilder()
            ->select('e AS demand')
            ->addSelect('IDENTITY(e.category) AS categoryId')
            ->addSelect('IDENTITY(e.city) AS cityId')
            ->addSelect('IDENTITY(e.region) AS regionId')
            ->addSelect('IDENTITY(city.country) AS countryId')
            ->addSelect('IDENTITY(city.administrativeCenter) AS administrativeCenterId')
            ->addSelect('IDENTITY(region.federalDistrict) AS federalDistrictId')
            ->addSelect('city.slug AS citySlug')
            ->addSelect('di')
            ->addSelect('e.viewsCount')
            ->addSelect('e.answersCount')
            ->from('MetalDemandsBundle:Demand', 'e')
            ->join('e.demandItems', 'di')
            ->join('e.category', 'cat')
            ->join('e.city', 'city')
            ->join('e.region', 'region')
            ->join('city.country', 'country')
            ->andWhere('city.displayInCountry IN (:countries_ids)')
            ->setParameter('countries_ids', Country::getEnabledCountriesIds())
            ->andWhere('e.moderatedAt IS NOT NULL')
            ->andWhere('e.deletedAt IS NULL');
    }

    private function prepareCountriesIds($mainCountryId)
    {
        $countriesIds = array($mainCountryId);
        // для всех стран, кроме России, добавляем ее id в массив. Что б их товары показывались бы на www
        if ($mainCountryId !== Country::COUNTRY_ID_RUSSIA) {
            $countriesIds[] = Country::COUNTRY_ID_RUSSIA;
        }

        return $countriesIds;
    }
}
