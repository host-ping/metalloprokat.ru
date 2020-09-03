<?php

namespace Metal\ProjectBundle\Doctrine;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;

class EntityRepository extends BaseEntityRepository
{
    /**
     * Load entities by ids and preserve given order.
     *
     * @param array $ids
     *
     * @return array
     */
    public function findByIds(array $ids, $indexById = false)
    {
        if (!$ids) {
            return array();
        }

        $idField = $this->getClassMetadata()->getSingleIdentifierFieldName();

        $qb = $this->_em->createQueryBuilder()
            ->select('e')
            ->from($this->_entityName, 'e', 'e.'.$idField);

        $entities = $qb
            ->where($qb->expr()->in('e.'.$idField, ':ids'))
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();

        $loadedEntities = array();
        foreach ($ids as $id) {
            if (!isset($entities[$id])) {
                continue;
            }

            if ($indexById) {
                $loadedEntities[$id] = $entities[$id];
            } else {
                $loadedEntities[] = $entities[$id];
            }
        }

        return $loadedEntities;
    }
}
