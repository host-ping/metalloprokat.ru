<?php

namespace Metal\ServicesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\ServicesBundle\Entity\Package;

class PackageRepository extends EntityRepository
{
    /**
     * @param bool $excludeBase
     *
     * @return Package[]
     */
    public function getPackages($excludeBase = false, $excludeStandart = false)
    {
        $qb = $this
            ->createQueryBuilder('p');

        if ($excludeBase) {
            $qb
                ->where('p.id <> :base_package')
                ->setParameter('base_package', Package::BASE_PACKAGE);
        }

        if ($excludeStandart) {
            $qb
                ->where('p.id <> :standart_package')
                ->setParameter('standart_package', Package::STANDARD_PACKAGE);
        }

        return $qb
            ->orderBy('p.priority', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getServiceItemsTree($excludeAdditionalPayment = false)
    {
        $serviceItems = $this->_em->getRepository('MetalServicesBundle:ServiceItem')
            ->findBy(array(), array('priority' => 'ASC'));

        $servicesItemsTree = array();
        foreach ($serviceItems as $serviceItem) {
            if ($excludeAdditionalPayment && $serviceItem->getAdditionalPayment()) {
                continue;
            }

            $servicesItemsTree[(int)$serviceItem->getParentId()][] = $serviceItem;
        }

        return $servicesItemsTree;
    }
}
