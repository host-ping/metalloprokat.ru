<?php

namespace Metal\DemandsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\AttributesBundle\Entity\DTO\LightAttributeValue;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\DemandsBundle\Entity\DemandItem;
use Metal\ProjectBundle\Util\InsertUtil;

class DemandItemAttributeValueRepository extends EntityRepository
{
    /**
     * @param AbstractDemand[]|\Traversable $demands
     */
    public function attachAttributesCollectionToDemands($demands)
    {
        $attributeValuesData = $this->_em
            ->getRepository('MetalDemandsBundle:DemandItemAttributeValue')
            ->createQueryBuilder('diav')
            ->select('IDENTITY(diav.attributeValue) as attributeValueId, demand.id as demandId')
            ->join('diav.demandItem', 'demandItem')
            ->join('demandItem.demand', 'demand')
            ->andWhere('demand.id IN (:demands)')
            ->setParameter('demands', $demands)
            ->getQuery()
            ->getResult();

        $attributeValueIds = array();
        $demandsPerAttributeValues = array();
        foreach ($attributeValuesData as $attributeValueData) {
            $attributeValueIds[$attributeValueData['attributeValueId']] = true;
            $demandsPerAttributeValues[$attributeValueData['attributeValueId']][] = $attributeValueData['demandId'];
        }

        $attributeValues = $this->_em->createQueryBuilder()
            ->addSelect(LightAttributeValue::getCreateDQL())
            ->from('MetalAttributesBundle:AttributeValue', 'av')
            ->join('av.attribute', 'a')
            ->where('av.id IN (:ids)')
            ->setParameter('ids', array_keys($attributeValueIds))
            ->addOrderBy('a.urlPriority')
            ->addOrderBy('av.urlPriority')
            ->getQuery()
            ->getResult();
        /* @var $attributeValues LightAttributeValue[] */

        $this->_em->getRepository('MetalAttributesBundle:AttributeValue')
            ->attachAttributesToLightAttributeValues($attributeValues);

        foreach ($attributeValues as $attributeValue) {
            $demandsIds = $demandsPerAttributeValues[$attributeValue->getId()];
            foreach ($demandsIds as $demandId) {
                $demandToAttributesValue[$demandId][] = $attributeValue;
            }
        }

        foreach ($demands as $demand) {
            $attributesCollection = new AttributesCollection();
            if (isset($demandToAttributesValue[$demand->getId()] )) {
                $attributesCollection->appendAttributeValues($demandToAttributesValue[$demand->getId()]);
            }
            $demand->setAttribute('demand_attributes_collection', $attributesCollection);
        }
    }

    /**
     * @param DemandItem[] $demandsItems
     */
    public function changeAttributeValues($demandsItems)
    {
        $oldDemandItemsAttributeValues = $this->_em
            ->getRepository('MetalDemandsBundle:DemandItemAttributeValue')
            ->createQueryBuilder('diav')
            ->select('diav.id, IDENTITY(diav.attributeValue) as attributeValueId, IDENTITY(diav.demandItem) as demandItemId')
            ->where('diav.demandItem IN (:demandsItems)')
            ->setParameter('demandsItems', $demandsItems)
            ->getQuery()
            ->getResult();

        $attributesValuesByDemandItems = array();
        foreach ($oldDemandItemsAttributeValues as $oldDemandItemAttributeValue) {
            $attributesValuesByDemandItems[$oldDemandItemAttributeValue['demandItemId']][$oldDemandItemAttributeValue['id']] = $oldDemandItemAttributeValue['attributeValueId'];
        }

        $demandsData = $this->_em
            ->getRepository('MetalDemandsBundle:DemandItem')
            ->createQueryBuilder('di')
            ->select('di.id, di.title')
            ->addSelect('IDENTITY(di.demand) AS demandId')
            ->addSelect('IDENTITY(di.category) AS categoryId')
            ->where('di.id IN (:demandsItems)')
            ->setParameter('demandsItems', $demandsItems)
            ->getQuery()
            ->getResult();

        //TODO: попробовать оптимизировать код
        $productParameterValueRepository = $this->_em->getRepository('MetalProductsBundle:ProductParameterValue');
        $rows = array();
        $toDeleteDemandItemAttributeValuesIds = array();
        foreach ($demandsData as $demandData) {
            $possibleAttributesValuesIds = array_column($productParameterValueRepository->matchAttributesForTitle($demandData['categoryId'], $demandData['title']), 'parameterOptionId');
            $oldAttributesValues = isset($attributesValuesByDemandItems[$demandData['id']]) ? $attributesValuesByDemandItems[$demandData['id']] : array();

            $toDeleteDemandItemAttributeValuesIds = array_merge($toDeleteDemandItemAttributeValuesIds, array_keys(array_diff($oldAttributesValues, $possibleAttributesValuesIds)));
            $toInsertAttributeValuesIds = array_diff($possibleAttributesValuesIds, $oldAttributesValues);

            foreach ($toInsertAttributeValuesIds as $attributeValueId) {
                $rows[] = array(
                    'demand_item_id' => $demandData['id'],
                    'attribute_value_id' => $attributeValueId,
                );
            }
        }

        $this->_em->createQueryBuilder()
            ->delete('MetalDemandsBundle:DemandItemAttributeValue', 'diav')
            ->where('diav.id IN (:ids)')
            ->setParameter('ids', $toDeleteDemandItemAttributeValuesIds)
            ->getQuery()
            ->execute();

        InsertUtil::insertMultipleOrUpdate($this->_em->getConnection(), 'demand_item_attribute_value', $rows, array(), 500);
    }

    /**
     * @param array $demandsIds
     *
     * @return array
     */
    public function getDemandsAttributes(array $demandsIds = array())
    {
        if (!$demandsIds) {
            return array();
        }

        $demandsToAttributes = array();
        foreach ($demandsIds as $demandId) {
            $demandsToAttributes[$demandId] = array();
        }

        $demandsAttributes = $this->_em->createQueryBuilder()
            ->from('MetalDemandsBundle:DemandItemAttributeValue', 'diav')
            ->join('diav.attributeValue', 'attributeValue')
            ->join('diav.demandItem', 'demandItem')
            ->join('demandItem.demand', 'demand')
            ->select('IDENTITY(attributeValue.attribute) AS attributeId')
            ->addSelect('attributeValue.id AS attributeValueId')
            ->addSelect('demand.id AS demandId')
            ->addSelect('IDENTITY(demandItem.category) AS categoryId')
            ->where('demand.id IN (:demands_ids)')
            ->setParameter('demands_ids', $demandsIds)
            ->getQuery()
            ->getResult();

        foreach ($demandsAttributes as $demandAttribute) {
            $demandId = $demandAttribute['demandId'];
            $attributeId = $demandAttribute['attributeId'];
            $attributeValueId = $demandAttribute['attributeValueId'];
            $categoryId = $demandAttribute['categoryId'];
            if (!isset($demandsToAttributes[$demandId][$categoryId][$attributeId])) {
                $demandsToAttributes[$demandId][$categoryId][$attributeId] = array();
            }

            if (!in_array($attributeValueId, $demandsToAttributes[$demandId][$categoryId][$attributeId])) {
                $demandsToAttributes[$demandId][$categoryId][$attributeId][] = $attributeValueId;
            }
        }

        return $demandsToAttributes;
    }
}
